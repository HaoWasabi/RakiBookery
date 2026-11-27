<?php

/**
 * Global Error & Exception Handler cho RakiBookery
 *
 * Bắt các lỗi PHP chưa được xử lý (fatal errors, uncaught exceptions, E_ERROR),
 * ghi log nội bộ và hiển thị trang lỗi thân thiện cho user.
 *
 * Cách hoạt động:
 *   1. set_exception_handler → bắt mọi Throwable chưa được catch
 *   2. set_error_handler     → bắt E_ERROR, E_WARNING, E_PARSE, ...
 *   3. register_shutdown_function → bắt fatal error (parse error, OOM, ...)
 *      vì fatal error không đi qua set_error_handler
 *
 * Biến môi trường:
 *   APP_ENV — môi trường (prod/staging/dev)
 */

// ─────────────────────────────────────────────────────────────
// 1. Uncaught Exception / Error handler
// ─────────────────────────────────────────────────────────────
set_exception_handler(function (Throwable $e): void {
    $msg = sprintf(
        '[EXCEPTION] %s: %s in %s on line %d',
        get_class($e),
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    );
    error_log($msg);
    error_log('[EXCEPTION] Stack trace: ' . _truncateTrace($e->getTraceAsString()));

    // Hiển thị lỗi thân thiện cho user
    _renderErrorPage(500, 'Có lỗi xảy ra, vui lòng thử lại sau.');
});

// ─────────────────────────────────────────────────────────────
// 2. PHP Error handler (E_WARNING, E_NOTICE, E_USER_ERROR, ...)
// ─────────────────────────────────────────────────────────────
set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline): bool {
    if (!(error_reporting() & $errno)) {
        return false;
    }

    error_log(sprintf(
        '[%s] %s in %s on line %d',
        _errorLevelName($errno),
        $errstr,
        $errfile,
        $errline
    ));

    // Trả false để PHP cũng ghi vào error_log bình thường
    return false;
});

// ─────────────────────────────────────────────────────────────
// 3. Shutdown handler — bắt fatal errors
// ─────────────────────────────────────────────────────────────
register_shutdown_function(function (): void {
    $error = error_get_last();
    if (!$error) {
        return;
    }

    $fatalTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR];
    if (!in_array($error['type'], $fatalTypes, true)) {
        return;
    }

    error_log(sprintf(
        '[PHP Fatal] %s: %s in %s on line %d',
        _errorLevelName($error['type']),
        $error['message'],
        $error['file'],
        $error['line']
    ));
});

// ─────────────────────────────────────────────────────────────
// Helper: tên readable của error level
// ─────────────────────────────────────────────────────────────
function _errorLevelName(int $level): string
{
    $map = [
        E_ERROR             => 'E_ERROR',
        E_WARNING           => 'E_WARNING',
        E_PARSE             => 'E_PARSE',
        E_NOTICE            => 'E_NOTICE',
        E_CORE_ERROR        => 'E_CORE_ERROR',
        E_CORE_WARNING      => 'E_CORE_WARNING',
        E_COMPILE_ERROR     => 'E_COMPILE_ERROR',
        E_COMPILE_WARNING   => 'E_COMPILE_WARNING',
        E_USER_ERROR        => 'E_USER_ERROR',
        E_USER_WARNING      => 'E_USER_WARNING',
        E_USER_NOTICE       => 'E_USER_NOTICE',
        E_STRICT            => 'E_STRICT',
        E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
        E_DEPRECATED        => 'E_DEPRECATED',
        E_USER_DEPRECATED   => 'E_USER_DEPRECATED',
    ];
    return $map[$level] ?? "UNKNOWN({$level})";
}

// ─────────────────────────────────────────────────────────────
// Helper: cắt ngắn stack trace (tránh log quá dài)
// ─────────────────────────────────────────────────────────────
function _truncateTrace(string $trace, int $maxLines = 10): string
{
    $lines = explode("\n", $trace);
    if (count($lines) <= $maxLines) {
        return $trace;
    }
    $kept = array_slice($lines, 0, $maxLines);
    $kept[] = '... (' . (count($lines) - $maxLines) . ' dòng bị cắt) ...';
    return implode("\n", $kept);
}

// ─────────────────────────────────────────────────────────────
// Helper: render trang lỗi đơn giản
// ─────────────────────────────────────────────────────────────
function _renderErrorPage(int $code, string $userMessage): void
{
    if (headers_sent()) {
        exit;
    }
    http_response_code($code);

    // Nếu request là AJAX/API → trả JSON
    $isApi = (
        (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
        (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))
    );

    if ($isApi) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $userMessage]);
    } else {
        echo "<!DOCTYPE html><html lang='vi'><head><meta charset='UTF-8'><title>Lỗi hệ thống</title></head>";
        echo "<body style='font-family:sans-serif;text-align:center;padding:60px'>";
        echo "<h2>Oops! Có lỗi xảy ra</h2>";
        echo "<p>{$userMessage}</p>";
        echo "<a href='/'>← Quay về trang chủ</a>";
        echo "</body></html>";
    }
    exit;
}

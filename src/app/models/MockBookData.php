<?php

/**
 * Dữ liệu sách giả lập dùng khi BOOK_DATA_SOURCE=mock.
 *
 * Trả về mảng có cùng cấu trúc với kết quả truy vấn từ DB,
 * để view không cần phân biệt nguồn dữ liệu.
 */
class MockBookData
{
    public static function getAll(): array
    {
        return [
            [
                'BookID'      => 1,
                'Name'        => 'Harry Potter and the Philosophers stone',
                'Author'      => 'J.K. Rowling',
                'Publisher'   => 'Bloomsbury Children',
                'ReleaseDate' => '2014-09-01',
                'Category'    => 'Văn học',
                'CategoryID'  => 1,
                'Price'       => 180000,
                'Stock'       => 120,
                'ImageURL'    => '/img/harry-potter-and-the-philosopher-stone.jpg',
                'Description' => 'Cuốn sách mở đầu cho loạt truyện về cậu bé phù thủy Harry Potter, theo chân Harry khi cậu khám phá ra thân phận thực sự của mình và bắt đầu học tại Trường Phù thủy và Pháp sư Hogwarts.',
                'Status'      => 1,
                'Length'      => '0.5 kg',
                'Weight'      => '13.2 x 3.6 x 20.1 cm',
                'Dimensions'  => 21,
                'Language'    => 'Tiếng Anh',
                'Format'      => 'Bìa cứng',
            ],
            [
                'BookID'      => 2,
                'Name'        => 'They Both Die At The End',
                'Author'      => 'Adam Silvera',
                'Publisher'   => 'Quill Tree Books',
                'ReleaseDate' => '2018-12-18',
                'Category'    => 'Văn học',
                'CategoryID'  => 1,
                'Price'       => 100000,
                'Stock'       => 120,
                'ImageURL'    => '/img/They Both Die At The End.jpg',
                'Description' => '"They Both Die at the End" là một tiểu thuyết dành cho thanh thiếu niên của Adam Silvera, xoay quanh chủ đề cuộc sống, cái chết và những kết nối ý nghĩa. Câu chuyện diễn ra trong một thế giới nơi một dịch vụ tên là Death-Cast thông báo cho mọi người biết ngày họ sẽ chết. Vào cùng một ngày, hai người xa lạ—Mateo và Rufus—nhận được cuộc gọi định mệnh và kết nối với nhau qua một ứng dụng có tên Last Friend. Trong ngày cuối cùng của cuộc đời, họ dần trở nên thân thiết và học cách sống trọn vẹn từng khoảnh khắc còn lại.',
                'Status'      => 1,
                'Length'      => '0.5 kg',
                'Weight'      => '19.8 x 13.2 x 2.5 cm',
                'Dimensions'  => 20,
                'Language'    => 'Tiếng Anh',
                'Format'      => 'Bìa mềm',
            ],
            [
                'BookID'      => 3,
                'Name'        => 'Bản thiết kế vĩ đại (the Grand Design)',
                'Author'      => 'Stephen Hawking, Leonard Mlodinow',
                'Publisher'   => 'NXB Trẻ',
                'ReleaseDate' => '2010-04-05',
                'Category'    => 'Khoa học',
                'CategoryID'  => 1,
                'Price'       => 130000,
                'Stock'       => 100,
                'ImageURL'    => '/img/ban-thiet-ke-vi-dai.jpg',
                'Description' => 'Cuốn sách khám phá lịch sử tri thức khoa học về vũ trụ, bắt đầu từ các triết gia Hy Lạp cổ đại đến các nhà khoa học hiện đại. Hawking và Mlodinow trình bày lý thuyết M-đa chiều, một nỗ lực nhằm thống nhất các lý thuyết vật lý hiện có để giải thích sự hình thành và bản chất của vũ trụ.',
                'Status'      => 1,
                'Length'      => '0.3 kg',
                'Weight'      => '14 x 20 cm',
                'Dimensions'  => 18,
                'Language'    => 'Tiếng Việt',
                'Format'      => 'Bìa mềm',
            ],
        ];
    }

    public static function getById(int $id): ?array
    {
        foreach (self::getAll() as $book) {
            if ($book['BookID'] === $id) {
                return $book;
            }
        }
        return null;
    }

    public static function getAvailable(): array
    {
        return array_values(array_filter(self::getAll(), fn($b) => $b['Status'] == 1));
    }

    public static function getTopBestSelling(int $limit = 5): array
    {
        // Dữ liệu mock không có lịch sử đơn hàng — trả về sách có status=1 với TotalSold giả
        $books = self::getAvailable();
        $mockSold = [320, 275, 190, 150, 88];

        foreach ($books as $i => &$book) {
            $book['TotalSold']    = $mockSold[$i] ?? rand(10, 100);
            $book['TotalRevenue'] = $book['TotalSold'] * $book['Price'];
        }

        usort($books, fn($a, $b) => $b['TotalSold'] <=> $a['TotalSold']);

        return array_slice($books, 0, $limit);
    }

    public static function getFiltered(string $categoryId = '', string $status = ''): array
    {
        return array_values(array_filter(self::getAll(), function ($book) use ($categoryId, $status) {
            if ($categoryId !== '' && $book['CategoryID'] != $categoryId) {
                return false;
            }
            if ($status !== '' && $book['Status'] != $status) {
                return false;
            }
            return true;
        }));
    }

    /** Danh sách category mock — dùng cho dropdown lọc */
    public static function getCategories(): array
    {
        return [
            ['CategoryID' => 1, 'Name' => 'Kỹ năng sống'],
            ['CategoryID' => 2, 'Name' => 'Tiểu thuyết'],
            ['CategoryID' => 3, 'Name' => 'Tâm lý học'],
            ['CategoryID' => 4, 'Name' => 'Lịch sử'],
        ];
    }
}

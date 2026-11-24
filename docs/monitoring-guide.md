# RakiBookery — Hướng dẫn hệ thống Monitoring & SNS Alerting

## Tổng quan kiến trúc

```
┌─────────────────────────────────────────────────────────────────┐
│                    LUỒNG 1: Lỗi Code PHP                        │
│                                                                 │
│  PHP App (ECS Fargate)                                          │
│  ┌────────────────────────────────────────────────────┐         │
│  │  index.php                                         │         │
│  │   └── error_handler.php (global handler)           │         │
│  │        ├── set_exception_handler()  ─┐             │         │
│  │        ├── set_error_handler()      ─┤→ SnsService │         │
│  │        └── register_shutdown_function() ─┘         │         │
│  └────────────────────────────────────────────────────┘         │
│           │ HTTP POST (NOTIFY_API_URL)                          │
│           ▼                                                     │
│    API Gateway ──► Lambda (NotifyLambda) ──► SNS Topic          │
│                                                  │              │
│                                                  ▼              │
│                                            Admin Email 📧        │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│              LUỒNG 2: Infrastructure Metric & Log               │
│                                                                 │
│  ECS Fargate ──► CloudWatch Logs ──► Metric Filters             │
│  RDS MySQL   ──► CloudWatch Metrics                             │
│                        │                                        │
│                        ▼                                        │
│               CloudWatch Alarms (10 cái)                        │
│                        │                                        │
│                        ▼                                        │
│                   SNS Topic ──────────────► Admin Email 📧       │
└─────────────────────────────────────────────────────────────────┘
```

---

## Các cảnh báo được cấu hình

### Lỗi Code PHP (Luồng 1 — Application)

| Trigger | Điều kiện | Email Subject |
|---------|-----------|---------------|
| Uncaught Exception | Mọi Throwable chưa được catch | `[RakiBookery][prod] Uncaught Exception: ExceptionClass` |
| PHP Fatal Error | E_ERROR, E_PARSE, E_CORE_ERROR | `[RakiBookery][prod] PHP FATAL: message...` |
| PHP User Error | E_USER_ERROR | `[RakiBookery][prod] PHP Error (E256): message...` |

> **Lưu ý:** Chỉ gửi SNS ở môi trường `prod` và `staging`. Môi trường `dev` chỉ hiển thị lỗi ra màn hình.

---

### Infrastructure Alarms (Luồng 2 — CloudWatch)

#### ECS Fargate

| Alarm | Ngưỡng | Điều kiện kích hoạt |
|-------|--------|---------------------|
| `ecs-cpu-high` | > 80% | CPU trung bình vượt ngưỡng 2 lần × 5 phút |
| `ecs-memory-high` | > 85% | Memory trung bình vượt ngưỡng 2 lần × 5 phút |
| `ecs-no-tasks` | < 1 task | Không có task nào đang chạy trong 3 phút |

#### RDS MySQL

| Alarm | Ngưỡng | Điều kiện kích hoạt |
|-------|--------|---------------------|
| `rds-cpu-high` | > 80% | CPU RDS trung bình vượt ngưỡng 2 lần × 5 phút |
| `rds-storage-low` | < 2 GB | Dung lượng còn lại dưới 2 GB |
| `rds-connections-high` | > 50 connections | Số kết nối vượt ngưỡng |
| `rds-read-latency-high` | > 1 giây | Read latency trung bình vượt 1s trong 10 phút |

#### Application Log Errors (CloudWatch Metric Filters)

| Alarm | Pattern bắt trong log | Ngưỡng |
|-------|----------------------|--------|
| `php-fatal-error` | `PHP Fatal error`, `Uncaught Exception`, `Fatal error:` | > 0 lần / 5 phút |
| `db-error` | `PDOException`, `SQLSTATE`, `Database error` | > 0 lần / 5 phút |
| `http-5xx-errors` | HTTP response 5xx | > 5 lần / 5 phút |

---

## Các file thay đổi / thêm mới

```
cloudformation-code/
├── raki-monitoring.yaml        ← THÊM MỚI: Stack SNS + Lambda + API GW + Alarms
├── raki-compute2.yaml          ← CẬP NHẬT: inject NOTIFY_API_URL vào ECS task
└── deploy-monitoring.sh        ← THÊM MỚI: script deploy tiện dụng

src/
├── config/
│   ├── error_handler.php       ← THÊM MỚI: global PHP error/exception handler
│   └── (không đổi các file khác)
├── public/
│   └── index.php               ← CẬP NHẬT: load error_handler.php
└── app/services/
    └── SnsService.php          ← CẬP NHẬT: thêm method notifySystemError()

.env.example                    ← CẬP NHẬT: thêm biến NOTIFY_API_URL
docs/
└── monitoring-guide.md         ← THÊM MỚI: tài liệu này
```

---

## Hướng dẫn deploy

### Bước 1: Deploy stack foundation và compute (nếu chưa có)

```bash
# Deploy foundation stack
aws cloudformation deploy \
  --template-file cloudformation-code/raki-foundation.yaml \
  --stack-name rakibookery-prod-foundation \
  --capabilities CAPABILITY_NAMED_IAM \
  --region ap-southeast-1

# Deploy compute stack
aws cloudformation deploy \
  --template-file cloudformation-code/raki-compute2.yaml \
  --stack-name rakibookery-prod-compute \
  --capabilities CAPABILITY_NAMED_IAM \
  --region ap-southeast-1
```

### Bước 2: Deploy monitoring stack

```bash
# Dùng script tiện dụng (khuyến nghị)
chmod +x cloudformation-code/deploy-monitoring.sh
./cloudformation-code/deploy-monitoring.sh your-admin@email.com prod

# Hoặc dùng AWS CLI trực tiếp
aws cloudformation deploy \
  --template-file cloudformation-code/raki-monitoring.yaml \
  --stack-name rakibookery-prod-monitoring \
  --parameter-overrides \
      AdminEmail=your-admin@email.com \
      Environment=prod \
  --capabilities CAPABILITY_NAMED_IAM \
  --region ap-southeast-1
```

### Bước 3: Xác nhận email subscription

Sau khi deploy, AWS SNS sẽ gửi email xác nhận đến địa chỉ admin. **Phải click link "Confirm subscription"** trong email đó để bắt đầu nhận cảnh báo.

### Bước 4: Lấy NOTIFY_API_URL và cập nhật ECS

```bash
# Lấy URL từ CloudFormation Output
NOTIFY_URL=$(aws cloudformation describe-stacks \
  --stack-name rakibookery-prod-monitoring \
  --region ap-southeast-1 \
  --query "Stacks[0].Outputs[?OutputKey=='NotifyApiUrl'].OutputValue" \
  --output text)

echo "NOTIFY_API_URL=$NOTIFY_URL"
```

Sau đó **redeploy ECS service** để task definition nhận biến môi trường mới:

```bash
aws ecs update-service \
  --cluster rakibookery-prod-cluster \
  --service rakibookery-prod-service \
  --force-new-deployment \
  --region ap-southeast-1
```

### Bước 5: Test hệ thống

```bash
# Test gửi SNS trực tiếp
TOPIC_ARN=$(aws cloudformation describe-stacks \
  --stack-name rakibookery-prod-monitoring \
  --region ap-southeast-1 \
  --query "Stacks[0].Outputs[?OutputKey=='AdminAlertTopicArn'].OutputValue" \
  --output text)

aws sns publish \
  --topic-arn "$TOPIC_ARN" \
  --subject "[Test] RakiBookery Monitoring" \
  --message "Đây là email test. Hệ thống monitoring hoạt động bình thường." \
  --region ap-southeast-1

# Test API Gateway endpoint (thay URL bằng giá trị thật)
curl -X POST "$NOTIFY_URL" \
  -H "Content-Type: application/json" \
  -d '{"subject":"[Test] PHP App Notify","message":"Test từ PHP app qua API Gateway"}'
```

---

## Điều chỉnh ngưỡng cảnh báo

Sửa Parameters trong `raki-monitoring.yaml` hoặc truyền qua `--parameter-overrides`:

```bash
aws cloudformation deploy \
  --template-file cloudformation-code/raki-monitoring.yaml \
  --stack-name rakibookery-prod-monitoring \
  --parameter-overrides \
      AdminEmail=your@email.com \
      ECSCpuAlarmThreshold=70 \
      ECSMemoryAlarmThreshold=80 \
      RDSCpuAlarmThreshold=75 \
      RDSConnectionsThreshold=30 \
  --capabilities CAPABILITY_NAMED_IAM \
  --region ap-southeast-1
```

---

## Thêm email nhận cảnh báo thứ hai

Thêm resource `AWS::SNS::Subscription` trong `raki-monitoring.yaml`:

```yaml
SecondAdminEmailSubscription:
  Type: AWS::SNS::Subscription
  Properties:
    TopicArn: !Ref AdminAlertTopic
    Protocol: email
    Endpoint: second-admin@email.com
```

---

## Chi phí ước tính (ap-southeast-1, production)

| Dịch vụ | Mức dùng ước tính | Chi phí/tháng |
|---------|-------------------|----------------|
| SNS | < 1,000 email/tháng | Free tier / ~$0 |
| Lambda | < 10,000 invocations | Free tier / ~$0 |
| API Gateway | < 10,000 requests | Free tier / ~$0 |
| CloudWatch Alarms | 10 alarms | ~$1.00 |
| CloudWatch Metric Filters | 3 filters | Free |
| **Tổng** | | **~$1/tháng** |

---

## Troubleshooting

### Admin không nhận email
1. Kiểm tra xem đã confirm SNS subscription chưa
2. Kiểm tra email spam folder
3. Kiểm tra `NOTIFY_API_URL` đã được set trong ECS task không:
   ```bash
   aws ecs describe-task-definition \
     --task-definition rakibookery-prod-app \
     --query "taskDefinition.containerDefinitions[0].environment"
   ```

### Lambda không được invoke
```bash
# Xem Lambda logs
aws logs tail /aws/lambda/rakibookery-prod-notify \
  --follow \
  --region ap-southeast-1
```

### CloudWatch Alarms không kích hoạt
1. Kiểm tra ECS Container Insights đã bật chưa (cần cho `RunningTaskCount` metric)
2. Với Fargate Spot, `RunningTaskCount` có thể cần bật Container Insights:
   ```bash
   aws ecs update-cluster \
     --cluster rakibookery-prod-cluster \
     --settings name=containerInsights,value=enabled
   ```
3. Metric Filters chỉ hoạt động với log mới (không phân tích log cũ)

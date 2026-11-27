#!/bin/bash
# ============================================================
# deploy-monitoring.sh
# Deploy hoặc update stack raki-monitoring (SNS + Lambda + Alarms)
#
# Cách dùng:
#   chmod +x deploy-monitoring.sh
#   ./deploy-monitoring.sh your-admin@email.com [prod|staging|dev]
# ============================================================

set -euo pipefail

ADMIN_EMAIL="${1:-}"
ENVIRONMENT="${2:-prod}"
PROJECT_NAME="rakibookery"
STACK_NAME="${PROJECT_NAME}-${ENVIRONMENT}-monitoring"
REGION="ap-southeast-1"
TEMPLATE_FILE="$(dirname "$0")/raki-monitoring.yaml"

# ── Validation ───────────────────────────────────────────────
if [[ -z "$ADMIN_EMAIL" ]]; then
  echo "❌  Thiếu tham số AdminEmail."
  echo "    Cách dùng: $0 your@email.com [prod|staging|dev]"
  exit 1
fi

if [[ ! "$ADMIN_EMAIL" =~ ^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$ ]]; then
  echo "❌  Email không hợp lệ: $ADMIN_EMAIL"
  exit 1
fi

# ── Deploy ───────────────────────────────────────────────────
echo "======================================================"
echo "  Deploying RakiBookery Monitoring Stack"
echo "  Stack      : $STACK_NAME"
echo "  Environment: $ENVIRONMENT"
echo "  Admin Email: $ADMIN_EMAIL"
echo "  Region     : $REGION"
echo "======================================================"

aws cloudformation deploy \
  --template-file "$TEMPLATE_FILE" \
  --stack-name "$STACK_NAME" \
  --parameter-overrides \
      ProjectName="$PROJECT_NAME" \
      Environment="$ENVIRONMENT" \
      AdminEmail="$ADMIN_EMAIL" \
  --capabilities CAPABILITY_NAMED_IAM \
  --region "$REGION" \
  --no-fail-on-empty-changeset

# ── Lấy Output ───────────────────────────────────────────────
echo ""
echo "✅  Deploy thành công!"
echo ""
echo "──────────────────────────────────────────────────────"
echo "  BƯỚC TIẾP THEO:"
echo ""

NOTIFY_URL=$(aws cloudformation describe-stacks \
  --stack-name "$STACK_NAME" \
  --region "$REGION" \
  --query "Stacks[0].Outputs[?OutputKey=='NotifyApiUrl'].OutputValue" \
  --output text 2>/dev/null || echo "")

if [[ -n "$NOTIFY_URL" ]]; then
  echo "  1. Gán biến môi trường NOTIFY_API_URL:"
  echo "     $NOTIFY_URL"
  echo ""
  echo "  2. Thêm dòng sau vào file .env:"
  echo "     NOTIFY_API_URL=$NOTIFY_URL"
  echo ""
fi

echo "  3. Vào email '$ADMIN_EMAIL' và xác nhận link subscription từ AWS SNS."
echo ""
echo "  4. Test gửi thông báo:"
echo "     aws sns publish \\"
echo "       --topic-arn \$(aws cloudformation describe-stacks \\"
echo "         --stack-name $STACK_NAME --region $REGION \\"
echo "         --query \"Stacks[0].Outputs[?OutputKey=='AdminAlertTopicArn'].OutputValue\" \\"
echo "         --output text) \\"
echo "       --subject '[Test] RakiBookery Alert' \\"
echo "       --message 'Đây là email test từ hệ thống monitoring RakiBookery.' \\"
echo "       --region $REGION"
echo "──────────────────────────────────────────────────────"

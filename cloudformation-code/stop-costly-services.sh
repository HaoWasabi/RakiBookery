#!/bin/bash

REGION="ap-southeast-1"
PROJECT="rakibookery"
ENV="prod"

echo "=============================================="
echo "  TẮT CÁC DỊCH VỤ PHÁT SINH CHI PHÍ"
echo "  Region: $REGION | Project: $PROJECT"
echo "=============================================="
echo

# 1. Dừng RDS Instance
echo ">>> [1/5] Đang dừng RDS Instance..."
RDS_ID="rakibookery-db-instance"

RDS_STATUS=$(aws rds describe-db-instances \
  --db-instance-identifier $RDS_ID \
  --region $REGION \
  --query 'DBInstances[0].DBInstanceStatus' \
  --output text 2>/dev/null)

if [ "$RDS_STATUS" == "available" ]; then
  aws rds stop-db-instance --db-instance-identifier $RDS_ID --region $REGION
  echo "    → Đã gửi lệnh Stop RDS: $RDS_ID"
elif [ "$RDS_STATUS" == "stopped" ]; then
  echo "    → RDS đã ở trạng thái stopped"
else
  echo "    → Không tìm thấy RDS hoặc trạng thái: $RDS_STATUS"
fi
echo

# 2. Scale ECS Service về 0 (tắt Fargate)
echo ">>> [2/5] Đang scale ECS Service về 0 task..."

CLUSTER_NAME="${PROJECT}-${ENV}-cluster"
SERVICE_NAME="${PROJECT}-${ENV}-service"

# Kiểm tra cluster có tồn tại không
CLUSTER_EXISTS=$(aws ecs describe-clusters --clusters $CLUSTER_NAME --region $REGION \
  --query 'clusters[0].status' --output text 2>/dev/null)

if [ "$CLUSTER_EXISTS" == "ACTIVE" ]; then
  aws ecs update-service \
    --cluster $CLUSTER_NAME \
    --service $SERVICE_NAME \
    --desired-count 0 \
    --region $REGION > /dev/null 2>&1

  if [ $? -eq 0 ]; then
    echo "    → Đã scale service $SERVICE_NAME về 0 task"
  else
    echo "    → Không tìm thấy service $SERVICE_NAME (có thể chưa deploy)"
  fi
else
  echo "    → Cluster $CLUSTER_NAME chưa tồn tại"
fi
echo

# 3. Xóa NAT Gateway (rất đắt tiền nếu có)
echo ">>> [3/5] Đang kiểm tra và xóa NAT Gateway..."

NAT_IDS=$(aws ec2 describe-nat-gateways \
  --filter "Name=tag:Name,Values=*${PROJECT}*" "Name=state,Values=available" \
  --region $REGION \
  --query 'NatGateways[*].NatGatewayId' \
  --output text 2>/dev/null)

if [ -n "$NAT_IDS" ]; then
  for NAT_ID in $NAT_IDS; do
    echo "    → Đang xóa NAT Gateway: $NAT_ID"
    aws ec2 delete-nat-gateway --nat-gateway-id $NAT_ID --region $REGION
  done
  echo "    → Đã gửi lệnh xóa NAT Gateway (mất vài phút mới xong)"
else
  echo "    → Không tìm thấy NAT Gateway nào"
fi
echo

# 4. Release Elastic IP không dùng
echo ">>> [4/5] Đang kiểm tra Elastic IP không gắn..."

EIPS=$(aws ec2 describe-addresses \
  --region $REGION \
  --query 'Addresses[?AssociationId==null].AllocationId' \
  --output text 2>/dev/null)

if [ -n "$EIPS" ]; then
  for EIP in $EIPS; do
    echo "    → Release Elastic IP: $EIP"
    aws ec2 release-address --allocation-id $EIP --region $REGION
  done
else
  echo "    → Không có Elastic IP nào đang thừa"
fi
echo

# 5. Tóm tắt các resource còn lại có thể tốn tiền
echo ">>> [5/5] Kiểm tra nhanh các resource còn lại..."

echo
echo "----- ALB đang chạy -----"
aws elbv2 describe-load-balancers \
  --region $REGION \
  --query 'LoadBalancers[*].[LoadBalancerName,DNSName,State.Code]' \
  --output table 2>/dev/null || echo "Không có ALB"

echo
echo "----- ECS Cluster -----"
aws ecs list-clusters --region $REGION --query 'clusterArns' --output table 2>/dev/null

echo
echo "----- RDS Status -----"
aws rds describe-db-instances \
  --region $REGION \
  --query 'DBInstances[*].[DBInstanceIdentifier,DBInstanceStatus,DBInstanceClass]' \
  --output table 2>/dev/null

echo
echo "=============================================="
echo "  HOÀN TẤT"
echo "=============================================="
echo
echo "Lưu ý:"
echo "  - RDS khi Stop vẫn tính phí storage (nhưng không tính compute)"
echo "  - ALB vẫn tính phí ngay cả khi không có traffic → muốn tắt hẳn thì phải xóa"
echo "  - NAT Gateway sau khi xóa sẽ mất IP public của private subnet"
echo
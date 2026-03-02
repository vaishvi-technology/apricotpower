#!/bin/bash

echo "Creating S3 bucket: ${BUCKET_NAME:-rcm}"

# Create the S3 bucket
awslocal s3 mb s3://${BUCKET_NAME:-rcm} --region ${AWS_DEFAULT_REGION:-us-east-1} 2>/dev/null || true

# Set bucket policy for public read access
awslocal s3api put-bucket-policy --bucket ${BUCKET_NAME:-rcm} --policy '{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "PublicReadGetObject",
            "Effect": "Allow",
            "Principal": "*",
            "Action": "s3:GetObject",
            "Resource": "arn:aws:s3:::'${BUCKET_NAME:-rcm}'/*"
        }
    ]
}'

# Enable CORS for the bucket
awslocal s3api put-bucket-cors --bucket ${BUCKET_NAME:-rcm} --cors-configuration '{
    "CORSRules": [
        {
            "AllowedHeaders": ["*"],
            "AllowedMethods": ["GET", "PUT", "POST", "DELETE", "HEAD"],
            "AllowedOrigins": ["*"],
            "ExposeHeaders": ["ETag"]
        }
    ]
}'

echo "S3 bucket ${BUCKET_NAME:-rcm} created and configured successfully"

# List buckets to verify
awslocal s3 ls

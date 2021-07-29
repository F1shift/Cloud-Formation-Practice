#!/bin/bash -xe
exec > >(tee /var/log/user-data.log|logger -t user-data -s 2>/dev/console) 2>&1
sudo yum update -y
sudo yum install -y ruby
sudo yum install -y wget
sudo yum install -y httpd
sudo amazon-linux-extras install -y php7.2
sudo yum install -y php php-mbstring
cd /home/ec2-user
wget https://aws-codedeploy-us-west-2.s3.us-west-2.amazonaws.com/latest/install
chmod +x ./install
sudo ./install auto
sudo systemctl start codedeploy-agent
sudo systemctl start httpd
sudo systemctl enable codedeploy-agent
sudo systemctl enable httpd
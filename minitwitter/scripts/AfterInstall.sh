#!/bin/bash
chmod 666 /var/www/html/tweets.txt
chmod 777 /home/ec2-user/scripts/DB-setup.sh
PROJECTNAME=`cat /home/ec2-user/project-name.txt`
sed -i "s/PROJECTNAME/${PROJECTNAME}/g" /var/www/html/minitwitter-rds.php
sed -i "s/PROJECTNAME/${PROJECTNAME}/g" /home/ec2-user/scripts/DB-setup.sh
service httpd start

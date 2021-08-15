#!/bin/bash
mysql -h db.PROJECTNAME.private.hostedzone -P 3306 -u minitwitteruser  -pminitwitterpass minitwitterdb < DB-setup.sql

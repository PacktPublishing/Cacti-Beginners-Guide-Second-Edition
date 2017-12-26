#!/bin/bash

# Set the backup filename and directory
DATE=`date +%Y%m%d` # e.g 20170608
FILENAME="cacti_database_$DATE.sql";
TGZFILENAME="cacti_files_$DATE.tgz";
BACKUPDIR="/backup/";
CACTIVER="1.1.23";

# Database Credentials
DBUSER="cactiuser";
DBPWD="MyV3ryStr0ngPassword";
DBNAME="cacti";

# Change to the root directory
cd /

# Where is our gzip tool for compression?
# The -f parameter will make sure that gzip will
# overwrite existing files
GZIP="/bin/gzip -f";

# Delete old backups older than 3 days
find /backup/cacti_*gz -mtime +3 -exec rm {} \;

# execute the database dump
mysqldump --user=$DBUSER --password=$DBPWD --add-drop-table --databases $DBNAME > $BACKUPDIR$FILENAME

# compress the database backup
$GZIP $BACKUPDIR$FILENAME

# Dump the rrd files to xml files
mkdir /tmp/xml 
cd /var/www/html/cacti/rra
for i in `find -name "*.rrd"` ; do rrdtool dump $i > /tmp/xml/$i.xml; done
cd /


# Create the Cacti files backup
tar -czpf $BACKUPDIR$TGZFILENAME --exclude='./var/www/html/cacti-$CACTIVER/rra' /etc/cron.d/cacti ./etc/php.ini ./etc/php.d ./etc/httpd/conf ./etc/httpd/conf.d ./etc/spine.conf ./usr/local/spine ./var/www/html/cacti ./var/www/html/cacti  ./etc/cacti/cactiwmi.pw  ./var/www/html/cacti-$CACTIVER ./etc/my.cnf ./etc/my.cnf.d ./tmp/xml


#!/bin/sh
cd /opt/pg94/data/pg_log
dname=$(date +"%y%m%d" --date="yesterday")
if [ ! -d "$dname" ]; then
        echo "Folder YESTERDAY is not existing..."
        exit 0
fi
echo Create Tar file for $dname
tar -zcvf ${dname}.tgz $dname
echo Delete Director for $dname
rm -Rf $dname


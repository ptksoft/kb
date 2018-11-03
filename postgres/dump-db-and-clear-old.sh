#!/bin/sh

# Setting
DB_NAME='DB_NAME'
DB_USER='DB_USER'
DB_PASS='DB_PASS'
DUMP_CMD='/opt/pg96/bin/pg_dump '
DUMP_PATH='/home/user/backup'

# Begin Operation
NOW=$(date +"%y%m%d")
YET2=$(date +"%y%m%d" --date="-2 day")
date
D1=$(date +"%Y-%m-%d %H:%M:%S")
echo Begin Dump database ${DB_NAME} as Compress-Custom File
echo Starting...
export PGPASSWORD="${DB_PASS}"
${DUMP_CMD} --format=c --file=${DUMP_PATH}/${DB_NAME}.$NOW.c.dump --clean --no-owner --insert --user=${DB_USER} ${DB_NAME}

echo Clear old 2 day backup ... ${DB_NAME}.${YET2}.c.dump
rm ${DUMP_PATH}/${DB_NAME}.${YET2}.c.dump
D2=$(date +"%Y-%m-%d %H:%M:%S")
echo Finish...

echo -- Time Begin Dump: $D1
echo -- Time End   Dump: $D2

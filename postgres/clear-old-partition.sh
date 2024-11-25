#!/bin/bash

# Define the PostgreSQL connection parameters
PGUSER="trs"
PGPASSWORD="trs"
PGDATABASE="trs"
PGHOST="localhost"
PGPORT="5432"

export PGPASSWORD

# Get the current date and calculate the cutoff (3 months ago)
current_year=$(date +%Y)
current_month=$(date +%m)
cutoff_date=$(date -d "$current_year-$current_month-01 -2 months" +"%Y%m")

# Connect to PostgreSQL and get the list of schemas
schemas=$(/opt/pg10/bin/psql -U "$PGUSER" -h "$PGHOST" -p "$PGPORT" -d "$PGDATABASE" -t -c "\dn" | awk '{print $1}')

date
for schema in $schemas; do
  # Check if the schema matches the pattern yYYYYMM
  if [[ $schema =~ ^y([0-9]{6})$ ]]; then
    schema_date=${BASH_REMATCH[1]}
    # Compare schema_date with cutoff_date
    if [[ $schema_date -lt $cutoff_date ]]; then
      echo "Dropping schema: $schema"
      /opt/pg10/bin/psql -U "$PGUSER" -h "$PGHOST" -p "$PGPORT" -d "$PGDATABASE" -c "DROP SCHEMA $schema CASCADE;"
    fi
  fi
done

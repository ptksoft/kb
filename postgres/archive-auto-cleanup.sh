#!/bin/bash

# Set the archive directory
ARCHIVE_DIR="/opt/pg10/data/archive"

# Find the oldest file than 3 day in the archive directory
OLDEST_FILE=$(find "$ARCHIVE_DIR" -type f -mtime +3 -not -name "*.*" | sort -r | head -n 1)

# Run pg_archivecleanup if an old file is found
if [[ -n "$OLDEST_FILE" ]]; then
        echo "Cleanup from point file [$OLDEST_FILE]"
        /opt/pg10/bin/pg_archivecleanup -d "$ARCHIVE_DIR" "$(basename "$OLDEST_FILE")"
else
        echo "No archive files older than 3 days were found in $ARCHIVE_DIR"
fi

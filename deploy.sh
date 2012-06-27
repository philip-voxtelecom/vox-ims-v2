#!/bin/bash

find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
mkdir templates_c cache
chmod 774 templates_c cache
chgrp apache -R *

echo "Please remember to check for config changes in configs/ and change database settings where required."

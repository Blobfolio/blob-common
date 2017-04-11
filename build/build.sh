#!/bin/bash
#
# Package as PHAR for cleaner distribution.

BUILD_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
BIN_DIR="$( dirname "${BUILD_DIR}" )/bin"



# Set up PharBuilder
echo -e ""
echo -e "-------------------"
echo -e "Cloning PharBuilder"
echo -e "-------------------"
if [ -e "${BUILD_DIR}/PharBuilder" ]; then
	rm -rf "${BUILD_DIR}/PharBuilder"
fi
git clone -q https://github.com/MacFJA/PharBuilder.git "${BUILD_DIR}/PharBuilder"
PHARBUILDER="${BUILD_DIR}/PharBuilder/bin/phar-builder"

if [ ! -e "$PHARBUILDER" ]; then
	echo -e "  + ERROR: Could not locate binary."
	exit 1
else
	echo -e "  + Clone succesful!"
fi

# Clean up
echo -e "  + Setting up application."
if [ -e "${BUILD_DIR}/PharBuilder/composer.lock" ]; then
	rm "${BUILD_DIR}/PharBuilder/composer.lock"
fi
$( cd "${BUILD_DIR}/PharBuilder" && composer install --no-dev --quiet -a )
if [ $? -ne 0 ]; then
	echo -e "  + ERROR: Composer failed."
	exit 1
else
	echo -e "  + Done!"
fi



# Set up Blob-Common
echo -e ""
echo -e "----------------------"
echo -e "Installing Blob-Common"
echo -e "----------------------"
if [ -e "${BUILD_DIR}/src" ]; then
	rm -rf "${BUILD_DIR}/src"
fi
mkdir "${BUILD_DIR}/src"
cp -a "${BUILD_DIR}/composer.json" "${BUILD_DIR}/src"
$( cd "${BUILD_DIR}/src" && composer install --no-dev --quiet -a )

if [ $? -ne 0 ]; then
	echo -e "  + ERROR: Composer failed."
	exit 1
else
	echo -e "  + Done!"
fi

# Clean up
find "${BUILD_DIR}/src/vendor" -name "composer.json" -delete
find "${BUILD_DIR}/src/vendor" -name "README.md" -delete
find "${BUILD_DIR}/src/vendor" -name "LICENSE" -delete
find "${BUILD_DIR}/src/vendor" -name ".gitattributes" -delete
find "${BUILD_DIR}/src/vendor" -name "docs" -type d -exec rm -rf {} +

echo -e "<?php require_once(dirname(__FILE__) . '/vendor/autoload.php'); ?>" > "${BUILD_DIR}/src/index.php"

echo -e ""
echo -e "-------------"
echo -e "Building Phar"
echo -e "-------------"

if [ -e "${BIN_DIR}/blob-common.phar" ]; then
	rm "${BIN_DIR}/blob-common.phar"
fi
if [ -e "${BIN_DIR}/version.json" ]; then
	rm "${BIN_DIR}/version.json"
fi

OUTPUT=$( cd "${BUILD_DIR}/src" && php -d phar.readonly=0 "${PHARBUILDER}" package -z --output-dir="${BIN_DIR}" --name="blob-common.phar" -s "yes" --entry-point="${BUILD_DIR}/src/index.php" "${BUILD_DIR}/src/composer.json" )
if [ $? -ne 0 ]; then
	echo -e "  + ERROR: Phar creation failed."
	exit 1
fi
if [ ! -e "${BIN_DIR}/blob-common.phar" ]; then
	echo -e "  + ERROR: Phar creation failed."
else
	DATE="$(date --iso-8601=seconds)"
	echo "{\"date\":\"$DATE\"}" > "${BIN_DIR}/version.json"
	echo -e "  + Done!"
fi



echo -e ""
echo -e "-------------------"
echo -e "Cleaning Up Sources"
echo -e "-------------------"
rm -rf "${BUILD_DIR}/src" "${BUILD_DIR}/PharBuilder"



echo -e "  + Done!"
exit 0
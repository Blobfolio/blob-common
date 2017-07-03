#!/bin/bash
#
# Package as PHAR for cleaner distribution.

BUILD_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
BIN_DIR="$( dirname "${BUILD_DIR}" )/bin"
SKEL_DIR="${BUILD_DIR}/skel"
SRC_DIR="${BUILD_DIR}/src"
OUT_DIR="${BUILD_DIR}/out"



# Some early clean up.
if [ -e "${SRC_DIR}" ]; then
	rm -rf "${SRC_DIR}"
fi
mkdir "${SRC_DIR}"
if [ -e "${OUT_DIR}" ]; then
	rm -rf "${OUT_DIR}"
fi
mkdir -p "${OUT_DIR}/lib"



# Set up Blob-Common
echo -e ""
echo -e "----------------------"
echo -e "Installing Blob-Common"
echo -e "----------------------"
echo -e "  + Compiling"
cp -a "${SKEL_DIR}/composer.json" "${SRC_DIR}"
$( cd "${SRC_DIR}" && composer install --no-dev --quiet -a )

if [ $? -ne 0 ]; then
	echo -e "  + ERROR: Composer failed."
	exit 1
fi

# Clean up
echo -e "  + Cleaning Up"
find "${SRC_DIR}/vendor" -name "composer.json" -delete
find "${SRC_DIR}/vendor" -name "README.md" -delete
find "${SRC_DIR}/vendor" -name "LICENSE" -delete
find "${SRC_DIR}/vendor" -name ".gitattributes" -delete
find "${SRC_DIR}/vendor" -name "docs" -type d -exec rm -rf {} +
find "${SRC_DIR}/vendor" -name "test" -type d -exec rm -rf {} +

# Build
echo -e "  + Packaging"
RESULT="$( php -d phar.readonly=0 "${SKEL_DIR}/build.php" )"
if [ ! -e "${BIN_DIR}/blob-common.phar" ]; then
	echo -e "  + ERROR: Phar creation failed."
else
	DATE="$(date --iso-8601=seconds)"
	CHECKSUM="$(md5sum "${BIN_DIR}/blob-common.phar")"
	CHECKSUM="${CHECKSUM:0:32}"
	echo "{\"date\":\"$DATE\",\"checksum\":\"$CHECKSUM\"}" > "${BIN_DIR}/version.json"
	echo -e "  + Done!"
fi


echo -e ""
echo -e "-------------------"
echo -e "Cleaning Up Sources"
echo -e "-------------------"
rm -rf "${SRC_DIR}" "${OUT_DIR}"



echo -e "  + Done!"
exit 0
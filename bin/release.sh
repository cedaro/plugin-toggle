#!/bin/bash

# Based on the GitHub to WordPress.org Deploy Script by Mike Jolley.
# https://github.com/mikejolley/github-to-wordpress-deploy-script

SLUG="plugin-toggle"
VERSION=$1

if [[ "" = "$VERSION" ]]; then
	read -p "Enter the version: " VERSION
fi

ARCHIVE="dist/$SLUG-$VERSION.zip"

# Make sure the package archive exists.
if [[ ! -f $ARCHIVE ]]; then
	echo "Package archive doesn't exist at $ARCHIVE."
	exit
fi

# Check out the SVN repository.
if [[ ! -d svn ]]; then
	svn checkout "https://plugins.svn.wordpress.org/$SLUG" svn
fi

# Update SVN.
svn update svn || { echo "Unable to update SVN."; exit 1; }

# Delete trunk.
rm -rf svn/trunk

# Unzip the distribution package.
unzip -q "dist/$SLUG-$VERSION.zip" -d svn

cd svn

# Move unzipped directory to trunk.
mv ${SLUG} trunk

# Add all unknown files to SVN.
svn add --force * --auto-props --parents --depth infinity -q

# Remove all deleted files from SVN.
MISSING_PATHS=$( svn status | sed -e '/^!/!d' -e 's/^!//' )

# Iterate over file paths.
for MISSING_PATH in $MISSING_PATHS; do
	svn rm --force "$MISSING_PATH"
done

# Tag trunk.
echo "Copying trunk to new tag."
svn copy trunk tags/${VERSION} || { echo "Unable to create tag."; exit 1; }

# Show status.
echo
echo "Showing SVN status."
svn status

# Confirm deploy.
read -p "Are you sure you want to release version $VERSION? " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
	exit 1
fi

# Deploy to WordPress.org.
echo
echo "Committing to WordPress.org. This may take a while..."
svn commit -m "Release $VERSION." || { echo "Unable to commit."; exit 1; }

cd ..
rm -rf svn

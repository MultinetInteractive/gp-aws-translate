#!/usr/bin/env bash

if [[ -z "$TRAVIS" ]]; then
	echo "Script is only to be run by Travis CI" 1>&2
	exit 1
fi

if [[ -z "$WP_PASSWORD" ]]; then
	echo "WordPress.org password not set" 1>&2
	exit 1
fi

if [[ -z "$TRAVIS_BRANCH" || "$TRAVIS_BRANCH" != "master" ]]; then
	echo "Build branch is required and must be a release-tag" 1>&2
	exit 0
fi


PLUGIN="gp-aws-translate"
PROJECT_ROOT=$TRAVIS_BUILD_DIR
VERSION="$(cat $PROJECT_ROOT/gp-aws-translate.php | grep Version: | head -1 | cut -d: -f2 | tr -d '[[:space:]]')"

echo "Version: $VERSION of $PLUGIN"

# Check if the tag exists for the version we are building
TAG=$(svn ls "https://plugins.svn.wordpress.org/$PLUGIN/tags/$VERSION")
error=$?
if [ $error == 0 ]; then
    # Tag exists, don't deploy
    echo "Tag already exists for version $VERSION, aborting deployment"
    exit 1
fi

# Remove files not needed in plugin for deployment
rm -f $PROJECT_ROOT/composer.json
rm -f $PROJECT_ROOT/.travis.yml
rm -f $PROJECT_ROOT/.gitignore
rm -fR $PROJECT_ROOT/scripts
rm -fR $PROJECT_ROOT/.git

# Make sure we are in the project root
cd $PROJECT_ROOT

# Go up one folder
cd ..

# Delete and recreate the deployFolder
rm -fR deployFolder
mkdir deployFolder

# Go into the deployFolder
cd deployFolder

# Clean up any previous svn dir
rm -fR svn

# Checkout the SVN repo
svn co -q "http://svn.wp-plugins.org/$PLUGIN" svn

# Copy our new version of the plugin into trunk
rsync -r -p -v --delete-before $PROJECT_ROOT/* svn/trunk

# Add new version tag
mkdir svn/tags/$VERSION
rsync -r -p -v --delete-before $PROJECT_ROOT/* svn/tags/$VERSION

# Add new files to SVN
svn stat svn | grep '^?' | awk '{print $2}' | xargs -I x svn add x@
# Remove deleted files from SVN
svn stat svn | grep '^!' | awk '{print $2}' | xargs -I x svn rm --force x@
svn stat svn

# Commit to SVN
svn ci --no-auth-cache --username $WP_USERNAME --password $WP_PASSWORD svn -m "Deploy version $VERSION"

# Remove SVN temp dir
rm -fR svn

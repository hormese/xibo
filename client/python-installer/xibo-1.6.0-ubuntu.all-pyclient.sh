#!/bin/bash

set -e

#    Script by Brian Mathis - http://www.brianmathis.net (c) 2012
#    Updated by Alex Harrington 2014

#    This script will update, download, and install the Python
#    client for use with Xibo - Open Source Digital Signage

#    This program is free software: you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation, either version 3 of the License, or
#    (at your option) any later version.

#    This program is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.

#    You should have received a copy of the GNU General Public License
#    along with this program.  If not, see http://www.gnu.org/licenses

#######################################################################
###
### Version 1.6.0 - 2/16/2014
### Changelog
###
### 1. Updated to use 1.6 branch of Xibo
###
### Version 1.4.0 - 10/24/2012
### Changelog
###
### 1. Updated to use 1.4 branch of Xibo
### 
### Version 1.3.3 - 8/16/2011
### Changelog
###
### 1. Removed support for 9.10
### 2. Added support for 11.10
### 3. Added libvdpau1 dependency
### 4. Changed binary tar download
### 5. Changed pulled version to 1.3
### 6. Removed running configuration utility (broken)
### 
### Version 1.3.2 - 2/14/2011
### Changelog
###
### 1. Added an option to run the configuration utility
### 2. Installing a new client will now run the configuration utility after client is installed
### 3. Changed bzr to pull from xibo/1.2
###
### Version 1.3.1 - 2/8/2011
### Changelog
###
### 1. Changed folder check directory to /opt/xibo/pyclient
### 2. Moved folder check to the beginning of install to stop script before downloading and packages
### 3. Added the -p option to continue if /opt/xibo exists
### 4. Checks if /opt/xibo/pyclient exists before attempting to update client
### 5. Added error if dir doesnt exist when updating
### 
### Version 1.3 - 1/29/2011
### Changelog
###
### 1. Added support for 11.04.
### 2. Added command to stop script on any error.
### 3. Added case menu to select install or update.
### 4. Checks if /opt/xibo already exists. If it does, the script will exit.
### 5. If updating, creates a backup of site.cfg, just in case.
### 
#######################################################################

# Checks to ensure the user is running as root since we will be making system wide changes

if [ ! "`whoami`" = "root" ]; then
		echo " "
		echo "========================================================"
		echo " This script must be run as root or with sudo... Exiting"
		echo "========================================================" 1>&2
		sleep 3
	exit 1
fi

echo " "
echo "========================================================"
echo " Xibo Python Client Installation Script v1.6"
echo " Ubuntu 12.04"
echo " Script by Brian Mathis - http://www.brianmathis.net (c)2010-2011"
echo " Updates by Alex Harrington (c) 2014"
echo " This script will update, download, and install the Python"
echo " client for use with Xibo - Open Source Digital Signage"
echo "========================================================"
echo " "
echo "Please select an option to continue..."
echo " "
echo "   [1] Install new client"
echo "   [2] Update client"
echo "   [q] Quit"
echo " "
echo " "

read OPTION

	case "$OPTION" in
		"1")

			# Stops the script if dir already exists. 
			if [ -d "/opt/xibo/pyclient" ]; then 

				echo "Error: /opt/xibo already exists. If you would like to upgrade"
				echo "your client, please choose option 2. Otherwise, please check"
				echo "the directory and try again."
				exit 1

			else

				# Creates dir
				mkdir -p /opt/xibo 

			fi

			# Check which version of Ubuntu the user is running and then selects what to do
			codename=`lsb_release -c | awk '{print $2}'`

			if [ "$codename" = "jaunty" ];

				then	echo "Ubuntu 9.04 is no longer officially supported by Canonical, please use Ubuntu 12.04" && exit 1

			elif [ "$codename" = "karmic" ];

				then	echo "Ubuntu 9.10 is no longer officially supported by Canonical, please use Ubuntu 12.04" && exit 1

			elif [ "$codename" = "lucid" ]
			
			    then	echo "Ubuntu 10.04 is no longer officially supported by Canonical, please use Ubuntu 12.04" && exit 1
			
			elif [ "$codename" == "precise" ]

				then
					echo " "
					echo "========================================================"
					echo " Installing Xibo Python Client for Ubuntu 12.04"
					echo " Required packages and binaries will now be installed"
					echo "========================================================"
					echo " "

					# Updates the package manager
					apt-get update > /dev/null

					# downloads the required files per http://xibo.org.uk/manual
					apt-get install -y libxss1 glade-gtk2 libboost-python1.46.1 libboost-thread1.46.1 libdc1394-22 libgtk2.0-0 libavutil51 bzr python-soappy python-feedparser python-serial flashplugin-installer libavcodec53 libavformat53 libswscale2 libsdl1.2debian libvdpau1 > /dev/null

					# downloads the binary files that Alex Harrington put together
					wget http://launchpad.net/xibo/1.6/1.6.0/+download/libavg-1.8.0-vdpau-berkelium11-12.04.tar.gz


			elif [ "$codename" = "maverick" ]

				then 	echo "Ubuntu 10.10 is not supported by Xibo, please us 12.04" && exit 1


			elif [ "$codename" = "natty" ]

				then	echo "Ubuntu 11.04 is not supported by Xibo, please us 12.04" && exit 1

			elif [ "$codename" = "oneiric" ]

				then 	echo "Ubuntu 11.10 is not supported by Xibo, please use 12.04" && exit 1

			else

				echo "Error: I cannot determine your version of Ubuntu." && exit 1

			fi


			# extracts the tar to /
			tar xf libavg-* -C /

			# removes tar file after extract
			rm libavg-*

			# runs ldconfig
			ldconfig

			# moves to dir and downloads pyclient
			# cd /opt/xibo && bzr co http://bazaar.launchpad.net/~xibo-maintainers/xibo/ponswinnecke 
			cd /opt/xibo && bzr co http://bazaar.launchpad.net/~xibo-maintainers/xibo/ponswinnecke pyclient

			# grants 777 read/write permissions to all
			chmod -R 777 /opt/xibo

			# opens configuration client by Matt Holder
			# cd /opt/xibo/pyclient/client/python/ && ./configure.py

			echo " "
			echo "========================================================"
			echo " All done. Navigate to /opt/xibo/pyclient/client/python"
			echo " to make changes to site.cfg "
			echo " For more info on the Python client, visit: "
			echo " http://xibo.org.uk/manual"
			echo "========================================================"
			echo " "

			sleep 3

		;;

		"2")
	
			# Checks to make sure Xibo is in /opt/xibo/pyclient. 
			if [ -d "/opt/xibo/pyclient" ]; then 

				echo " "		
				echo "========================================================"
				echo " Creating a backup of site.cfg and updating the"
				echo " Xibo bzr branch. "
				echo "========================================================"
				echo " "
	
				sleep 3

				# Before pulling, make a backup of site.cfg
				cp /opt/xibo/pyclient/client/python/site.cfg /opt/xibo/pyclient/client/python/site.cfg.backup

				# Move to the install dir
				cd /opt/xibo/pyclient

				# Pull the latest and greatest from bzr
				bzr pull > /dev/null

				echo " "
				echo " "
				echo "========================================================"
				echo " Done updating your client."
				echo "========================================================"
				echo " "

				sleep 3
	
			else

				echo " "
				echo "Error: /opt/xibo/pyclient does not exist. Was it installed somewhere else?"
				echo " "

			fi

		;;

		"Q"|"q")

			echo " "
			echo "Thanks for using Xibo - Open Source Digital Signage"
			echo " "
			echo " "

		;;
 esac

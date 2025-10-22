NOTE:
This repository is a copy of the code from the Moodle plugins db, with code from Jeff Sherk's "quickenrol" fork merged in.
I have not reviewed this plugin completely and do not endorse it personally. I put this in github here to help with a proof of concept site and am not using this code in production.
(Dan Marsden March 2017)

I have reverted changes back to original before I have made my amendments, these have been made for individual requirements and general Moodle updates to be in line with Moodle 4.5 LTS:
 1. Block visibility expanded to include all users with permission to edit other user profiles.
 2. Block expanded use to contain other admin 'Quick Links', updated to work on Moodle 4.5.X with Font-Awesome v5.
 3. Display changes in user data for selected users on the form - name fields seperated better, user email shown.
 4. Courses to enrol onto only pulls from a selected Parent Category.
 5. New strings added into lang pack and used on enrolment page.
 6. Security, and HTML-escaping/encoding fixes added that function with Moodle 4.5.X's stricter data handling
(Andy Shaw - October 2025)

------------
These instructions describe how to install the Enrollment block for Moodle 2+.  This module is developped and supported by Symetrix.

With this plugin you can quickly enroll users to many courses.

Prerequisites:
============
You need a:

1.  A server running Moodle 2.0+

2.  A browser with javascript enabled

Installation
============

These instructions assume your Moodle server is installed at /var/www/moodle.

1.  Copy enrollment.zip to /var/www/moodle/blocks


2.  Enter the following commands

	cd /var/www/moodle/blocks
    	sudo unzip enrollment.zip

    This will create the directory
 
        ./enrollment

3.  Login to your moodle site as administrator

	Moodle will detect the new module and prompt you to Upgrade.
	


4.  Click the 'Upgrade' button.  

	The activity module will install block_enrollment.


5.  Click the 'Continue' button. 

At this point, you can add enrollment block on pages.


If you have feedback or any questions, contact us at

	http://www.symetrix.fr/

Regards,... Adrien Jamot
adrien_jamot [at] symetrix [dt] fr







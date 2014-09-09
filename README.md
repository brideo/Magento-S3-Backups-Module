
##Introduction

Unfortunately lower versions of Magento don't actually have the feature to automate you databases backups, I believe they introduced this in Magento 1.7.

When I coded this solution I had to roll it out on multiple Magento stores of varied versions so I have assumed that the feature to automate backups is not enabled. If you have configured these backups using Magento's cron you will need to disable this, otherwise I have released this code on GitHub if you would like to adapt it to suit your needs.

This module will automatically create a backup of your Magento database at your chosen time, transfer the backup to Amazon S3 and then remove it from your server.

##Setting up the Magento Cron

If you haven't setup the Magento Cron's to run you will need to do this. I am using a CenTOS server with a LAMP setup. Here is how you setup the Magento Cron on most Linux based servers, once you are logged in:

	crontab -e

Now add this:

	*/5 * * * * /bin/sh /path/to/your/magento/installation/cron.sh
This will mean the Magento crons are hit every 5 minutes.

##Adding the Amazon S3 Library

So Donovan Schönknecht, or Typo, has made a really great Amazon S3 PHP Class we can add to our Magento Lib folder. To find this please see his [Amazon S3 PHP Class Github](https://github.com/tpyo/amazon-s3-php-class) page, you will need to download S3.php from there.

Once you have done this you will need to add the file to your Magento installation:

	lib/Amazon/S3.php

I have put it in an 'Amazon' directory, you may want to name it 'Typo' or 'Undesigned' but that is up to you, just take a not for when you require this lib later.

##Adding The Magento Module

All the files can be found on my GitHub, however I will go through each step in this post. I have used a local code pool setup, feel free to change this to community.

The files we will be adding are:

	app/etc/Brideo_Amazon.xml
	app/code/local/Brideo/Amazon/etc/config.xml
	app/code/local/Brideo/Amazon/etc/system.xml
	app/code/local/Brideo/Amazon/Block/S3.php
	app/code/local/Brideo/Amazon/Helper/Data.php

##Setting Up The Configuration

To set up the configuration you will need to first clear the cache, then go to System > Configuration > Amazon > S3 Backups.

You can get your **Amazon Access Key**  and **Amazon Secret Key** from the Amazon console, the **Bucket Name** is the bucket you create yourself in Amazon S3. Once you have enabled the module continue to the next step.

##Manually Testing the Module

Ideally you don't want to be waiting around for a cron job to run to test your  backups are syncing. To manually test the backup you will need to call the function which initiates the backups, this can be run from any page and should be done in a development environment.

	Mage::getModel('amazon/s3')->prepareBackup();


##Changing The Time Which The Backup Runs

I have set up this module to run at 1am by default as this is when our website aren't very busy, to change this you will need to edit:

		app/code/local/Brideo/Amazon/etc/config.xml

Find the line which represents the cron expression and edit it to suit your needs;

	<schedule><cron_expr>0 1 * * *</cron_expr></schedule>


##Side Note:

When I unzip the files I need to put .sql on the end for them to work, you can tamper with the code to fix this but I haven't bothered.

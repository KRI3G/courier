# Courier
  - A delivery system database and website for use in IT tech shops

## Ideas
* Signature Pad
* ~~Automatically keep track of entered and delivered time~~
* Pictures
  - Read serial numbers?
* Sync with TDx?
* Sync with phonebook?
  - UIN
  - Name
* Reccommended entries
* Run on both desktop and mobile
  - Desktop to input
  - Mobile to send
* ~~Add related items to deliver (Aka computers)~~
* ~~Add notes to the delivery~~
* User access control
  - Authenticate with Howdy?
* Make details of entry editable
  - KEEP LOGS THO
* Search by filters
* DOCUMENTATION

## Process

1. Get the basics down
  - LAMP setup on local machine
    - Might want to set up TLS between webserver and MariaDB
  - Pull and push data from website to database
2. Sites
  - / - Dashboard for available orders
  - /create - create a new entry for an order
  - /order?= - selection for current order
  - /admin - user access control, etc.
  + Also, dashboard style with pop up from left side, good for mobile users

## What I've done (aka, what to document)
* Install LAMP onto RHEL
  - Set up Apache
  - Set up PHP
  - Setting up MariaDB
    - Database "delivery_log", "table orders", refer to /Documentation/databaseSchema.txt
      1. orderID
      2. Date and time received 
      3. Received by
      4. Tracking number (Amazon, AB number, etc)
      5. Items in order and quantities
      6. Serial numbers, in order
      7. Current location
      8. Date and time delivered
      9. Delivered to
      10. Delivered by
      11. Ticket number
      12. Requestor name
      13. Status
      14. Notes
    - Signatures will be pulled from orderID
  - Built the sites
    - Built index.php
      - Used as a dashboard for all available orders
    - Built create.php
      - Used to create new entries into the database
      - Need to add identification
    - Built edit.php
      - Got edit options working
      - Need to add signatures and images
      - Also identification

## Security Features (MUST IMPLEMENT)
- Edit page
  - Make anything that should not be editable, completely uneditable (aka, use php, don't rely on html/js)
- SQL Injection Prevention
  - On **EVERY** page
- Federated authentication
  - Using TAMUs Entra ID API
  - User access control/Authorization eforcement (to create, edit, and even see available orders)
- Database dump
  - Dump the database into a read/pull protected network drive on a scheduled basis
  - Same with signatures (?)
  - This way even I can't modify the backups of the database
  - Also great to have in case of server dying (REDUNDANCY)
- Log every edit to orders (?)
  - Create parallel table with non-unique orderIDs and TIMESTAMPs of changes?
      
* Used https://github.com/szimek/signature_pad
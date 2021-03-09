# mobtexting-civicrm
MOBtexting SMS API Extension plugin for CiviCRM

# Set-up

* In this chapter, the steps required to set up an SMS gateway will be explored. Once configured, you will be able to send both single and mass text messages to individual contacts which have a mobile phone number defined, ie. Phone Type must be `Mobile`.

```
Note: Contacts which do not have a mobile phone number defined will not receive a SMS text message.

```

### Configuring a MOBtexting SMS Gateway ###
Registering for a MOBtexting account 
[register](https://portal.mobtexting.com/register) here

Once you have registered yo will get  `Access Token` key, `Service` type and `Sender` type values.

# CiviCRM Set-up
1. Download the extension MOBtexting SMS package from [here](https://github.com/mobtexting/mobtexting-civicrm/archive/master.zip)
    Unzip / under the package and place `downloaded sms package` in your configured extensions directory. If you don't know the extension directory see the image how to get the extension directory. 

    <img src="/images/1.png">

2. If the package has been constructed properly - when you reload the Manage Extensions page the new extension should be listed with an Install link click and install it.

    <img src="/images/2.png">

### Completing the SMS Provider settings in CiviCRM

* You now have all of the information needed to configure SMS in CiviCRM. To continue, return to CiviCRM and go to: `Administer > System Settings> SMS Providers`.
Click `Add New Provider`.

    <img src="/images/apiconfig.png">

**Complete the following settings:**

* `Name`: Select `MOBtexting`
* `Title`: Give the SMS provider a title user's will see (e.g. `MOBtexting SMS`)
* `Username`: Enter your username (optional)
* `Password`: Enter your password (optional)
* `API type`: Select `http`
* `API URL`: Type the URL as follows: `https://portal.mobtexting.com/api/v2/sms/send`
* `API Parameters`: This are where you should provide your Access Token, Service, Sender Fields and Values. The format required are:      
``` 
    access_token= xxxxxxxxxxxxxxx
    service =  x 
    sender = xxxxxx
```
`Give the same format and the correct values`.

* Is this provider active?: Tick to enable the SMS gateway
* Is this a default provider?: Check this option to make it the default, where multiple SMS providers are available

CiviCRM will now be configured to send text messages to your contacts.

<img src="/images/4.jpg">




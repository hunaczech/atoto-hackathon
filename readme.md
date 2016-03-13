atoto.cz hackhaton - image storage server
=============

Upload new image
 - POST to http://images.hunka.cz/atoto/image/
 - Request:
 
```
{
    "url": "http://nickcavarretta.com.au/wp-content/uploads/2013/08/385085_394660707279936_1403636279_n.jpg"
}
```

 - Result:

```
{
"status":{
	"id":"b88c249049711d9b79295025d318caa6",
	"filesize":34766,
	"updated":{
		"date":"2016-03-12 21:42:12.000000",
		"timezone_type":3,
		"timezone":"Europe/Prague"
		}
	},
"inputValidation":{
	"valid":true
	}
}
```


Retrieve Image

 - GET to http://images.hunka.cz/atoto/image/{imageId}[/{profile}]

How to specify profiles ?

`app/config/profiles.json`

Can I use placeholder ?

`app/config/profiles.json` again :-)
 

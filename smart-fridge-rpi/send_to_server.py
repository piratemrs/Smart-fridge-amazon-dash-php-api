import requests
url = 'https://dkyf2jsa9ujaq.cloudfront.net/api.php?email=xxxx@email.com&'
import webbrowser
data=''
#webbrowser.open(url)
d = {}
r = {}
with open("./sift/result.txt") as f:
    for line in f:
       (key, val) = line.split()
       d[(key)] = int (val)
for k,v in d.items():
    data+= k+"="+str(v)+"&"
    print data

with open("./data.txt") as j:
    for line in j:
       (key, val) = line.split()
       r[(key)] = int (val)
for l,m in r.items():
    data+= l+"="+str(m)+"&"
    print data
url_to_send = url+data[:-1]
print url_to_send
if "tangerine" not in url_to_send: 
    url_to_send+="&tangerine=0"
if "cantaloupe" not in url_to_send:
   url_to_send+="&cantaloupe=0"
if "yogurt" not in url_to_send:
   url_to_send+="&yogurt=0"

print url_to_send
   
# GET
r = requests.get(url_to_send)
# Response, status etc
print r.text
print r.status_code



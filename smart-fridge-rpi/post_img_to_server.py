import requests
import webbrowser
import base64

url = 'https://dkyf2jsa9ujaq.cloudfront.net/api.php?e=u&'


with open("./frame.jpg", "rb") as image_file:
    encoded_string = base64.b64encode(image_file.read())
    print encoded_string
    data={'setimage':encoded_string,'email':'xxxx@email.com'}


r = requests.post(url,data,verify=False)
# Response, status etc
print r.text
print r.status_code



import time
import RPi.GPIO as GPIO
import cv2
import time
import os
from shutil import copyfile

newCapture=False
GPIO.setmode(GPIO.BOARD)
GPIO.setup(23, GPIO.IN)
GPIO.setup(11, GPIO.OUT)

def send_data_to_server():
	os.system('python send_to_server.py')
def send_img_to_server():
	os.system('python post_img_to_server.py')
def run_color_detector():
	os.system('sudo ./main')
def run_sift():
	os.system('python ./sift/detect.py')
def cap():
	cv2.namedWindow("camera")

	capture = cv2.VideoCapture(0)

	i = 0
	while GPIO.input(23) == True:
		
		flag,img=capture.read()
		#img = cv.fromarray(im_array)
		if(i%60==0):
			cv2.imshow("camera", img)
			cv2.imwrite('pic{:>09}.jpg'.format(i), img)
			if cv2.waitKey(10) == 27:
				break
		i += 1

def select_frame_and_remove_others():
	
	images=[]
	for file in os.listdir("./"):
		if file.endswith(".jpg"):
			images.append(file);
			
	images.sort()
	print(images)
	try:
		images[-2]
	except IndexError:
		print("Insufficient Images Captured")
		return
	print(images[-2])
	saved_image = images[-2]
	images.remove(images[-2])
	for f in images:
		os.remove(f)
	print(images)
	print ("saved image =")
	print(saved_image)
	os.rename(saved_image,"frame.jpg")
	copyfile("frame.jpg","./images/frame.jpg")

while True:
	if (GPIO.input(23) == True):
		newCapture=True
		GPIO.output(11,1)
		cap()
	else:
		GPIO.output(11,0)
		
		if(newCapture):
			newCapture=False
			select_frame_and_remove_others()
			run_sift()
			run_color_detector()
			send_img_to_server()
			send_data_to_server()
			
			

import cv2

global PlayVideo
global frame

refPt = []
cropping = False

def click_and_crop(event, x, y, flags, param):
	# grab references to the global variables
	global refPt, cropping
 
	# if the left mouse button was clicked, record the starting
	# (x, y) coordinates and indicate that cropping is being
	# performed
	if event == cv2.EVENT_LBUTTONDOWN:
		refPt = [(x, y)]
		cropping = True
 
	# check to see if the left mouse button was released
	elif event == cv2.EVENT_LBUTTONUP:
		# record the ending (x, y) coordinates and indicate that
		# the cropping operation is finished
		refPt.append((x, y))
		cropping = False
 
		# draw a rectangle around the region of interest
		cv2.rectangle(frame, refPt[0], refPt[1], (0, 255, 0), 2)
		cv2.imshow("preview", frame)


PlayVideo = True;

cv2.namedWindow("preview")
cv2.setMouseCallback("preview", click_and_crop)
vc = cv2.VideoCapture(0)
if vc.isOpened() and PlayVideo: # try to get the first frame
    rval, frame = vc.read()
    
else:
    rval = False


while rval:
    
    cv2.imshow("preview", frame)
    if PlayVideo:
        rval, frame = vc.read()
        clone=frame.copy()
    else:
        cv2.setMouseCallback("preview", click_and_crop)
    key = cv2.waitKey(20)
    if key == ord('c') and PlayVideo==False: 
        if len(refPt) == 2:
                roi = clone[refPt[0][1]:refPt[1][1], refPt[0][0]:refPt[1][0]]
                cv2.imshow("ROI", roi)
                imgname=raw_input('Please Enter Image name to save the product\n')
                cv2.imwrite("images/"+imgname+".jpg", roi)
                print(imgname+".jpg Save Successfully to Images folder")
                cv2.waitKey(0)
    elif key ==27: 
            break
    elif key==ord('a'):
        if PlayVideo== True:
            PlayVideo=False
            print('a pressed'+str(PlayVideo))
        elif PlayVideo==False:
            PlayVideo=True
            print('a pressed'+str(PlayVideo))
       

if len(refPt) == 2:
	roi = clone[refPt[0][1]:refPt[1][1], refPt[0][0]:refPt[1][0]]
	cv2.imshow("Cropped Image", roi)
	imgname=raw_input('Please Enter Image name to save the product\n')
	cv2.imwrite("images/"+imgname+".jpg", roi)
	print(imgname+".jpg Save Successfully to Images folder")
	cv2.waitKey(0)
 
vc.release()
cv2.destroyAllWindows()


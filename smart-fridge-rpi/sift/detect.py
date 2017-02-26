import copy
import numpy as np
import cv2
import os

global names
names= []

MIN_MATCH_COUNT = 15
FLANN_INDEX_KDTREE = 0
MAX_PRODUCTS = 10
count=0;
detector = cv2.xfeatures2d.SIFT_create()
index_params = dict(algorithm = FLANN_INDEX_KDTREE, trees = 5)
search_params = dict(checks = 50)
flann = cv2.FlannBasedMatcher(index_params, search_params)
print(cv2.__version__)
def detect(filename):
    img2 = cv2.imread(filename,0)
    img2 = cv2.resize(img2, (0,0), fx=0.5, fy=0.5)
    img2 = np.uint8(img2)
    print (img2.shape)
    
    rects = []

    for i in range(0, MAX_PRODUCTS):
        mx = 0
        mimg = None
        mkp = None
        mdes = None
        mgood = []

        kp2, des2 = detector.detectAndCompute(img2,None)

        for root, dirs, files in os.walk('products/'):
            for name in files:
                print ("Processing "+name.rsplit('.', 1)[0]+' Now')
                img1 = cv2.imread('products/'+name,0)
                kp1, des1 = detector.detectAndCompute(img1,None)
                matches = flann.knnMatch(np.asarray(des1,np.float32),np.asarray(des2,np.float32), 2)

                good = []
                
                for m,n in matches:
                    if m.distance < 0.35*n.distance:
                        good.append(m)

                if len(good)>mx:
                    
                    mx = len(good)
                    if mx>MIN_MATCH_COUNT:
                        names.append(name)
                        print(name.rsplit('.', 1)[0]+" Found")
                    mimg = img1
                    mkp = kp1
                    mdes = des1
                    mgood = good

                del img1
                del kp1
                del des1
                del matches
                del good

            if mx>MIN_MATCH_COUNT:
                
                global count
                count += 1
                src_pts = np.float32([ mkp[m.queryIdx].pt for m in mgood ]).reshape(-1,1,2)
                dst_pts = np.float32([ kp2[m.trainIdx].pt for m in mgood ]).reshape(-1,1,2)

                M, mask = cv2.findHomography(src_pts, dst_pts, cv2.RANSAC,5.0)
                if mask==None:
                    return copy.deepcopy(rects)

                matchesMask = mask.ravel().tolist()

                h,w = mimg.shape
                pts = np.float32([ [0,0],[0,h-1],[w-1,h-1],[w-1,0] ]).reshape(-1,1,2)
                dst = cv2.perspectiveTransform(pts,M)

                min_x = 9999999
                max_x = 0
                min_y = 9999999
                max_y = 0

                for i in range(0, 4):
                    min_x = max(0, min(min_x, dst[i][0][0]))
                    max_x = min(img2.shape[1]-1, max(max_x, dst[i][0][0]))
                    min_y = max(0, min(min_y, dst[i][0][1]))
                    max_y = min(img2.shape[0]-1, max(max_y, dst[i][0][1]))

                rect = {"x":int(min_x), "y":int(min_y), "w":int(max_x-min_x), "h":int(max_y-min_y)}
                rects.append(rect)
                #print (rects)
                img2[rect["y"]:rect["y"]+rect["h"], rect["x"]:rect["x"]+rect["w"]] = 0
                
                img2 = cv2.polylines(img2,[np.int32(dst)],True,(0,120,82),3, cv2.LINE_AA)
                cv2.imwrite("out.jpg", img2)

            else:
                cv2.imwrite("out.jpg", img2)
                print ("Not enough matches are found - %d/%d" % (len(mgood),MIN_MATCH_COUNT))
                matchesMask = None
                return rects

            draw_params = dict(matchColor = (0,255,0),
                               singlePointColor = None,
                               matchesMask = matchesMask,
                               flags = 2)
            print('count = '+str(count))



    return rects

def saveOutput():
    my_count = {}
    for word in names:
        try: my_count[word] += 1
        except KeyError: my_count[word] = 1
    with open("result.txt") as input:
        lines = [line for line in input if line.strip()]
    with open("result.txt", "w") as output:
        for line in lines:
            output.write(line)
        output.write("\n")
        for key,value in my_count.items():
            output.write((str(key).rsplit('.', 1)[0]+" "+str(value))+"\n")
        

'''for root, dirs, files in os.walk('frames/'):
	for name in files:
		print('frames/'+name)
		re=products('frames/'+name)
		print(re)
		'''
detect("frame.jpg")
img=cv2.imread('out.jpg')
saveOutput()
cv2.putText(img,'count = '+str(count),(246,25), cv2.FONT_HERSHEY_SIMPLEX , 1,(0,0,0),2)
#cv2.imshow('out.jpg',img)
cv2.waitKey(0)
cv2.destroyAllWindows()



//Written by  Kyle Hounslow 2013

//Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software")
//, to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, 
//and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

//The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

//THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
//FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER 
//LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
//IN THE SOFTWARE.

#include <set>
#include <algorithm>
#include <sstream>
#include <string>
#include <iostream>
#include<vector>
#include"fruit.h"
#include <time.h>
#include <stdio.h>
#include <fstream>

using namespace std;
using namespace cv;

int H_MIN = 0;
int H_MAX = 256;
int S_MIN = 0;
int S_MAX = 256;
int V_MIN = 0;
int V_MAX = 256;
vector<fruit>  apples;


IplImage *frame;
int frames;

//default capture width and height
const int FRAME_WIDTH = 640;
const int FRAME_HEIGHT = 480;
//max number of objects to be detected in frame
const int MAX_NUM_OBJECTS = 50;
//minimum and maximum object area
const int MIN_OBJECT_AREA = 20 * 20;
const int MAX_OBJECT_AREA = FRAME_HEIGHT*FRAME_WIDTH / 1.5;
//names that will appear at the top of each window
const string windowName = "Original Image";
const string windowName1 = "HSV Image";
const string windowName2 = "Thresholded Image";
const string windowName3 = "After Morphological Operations";
const string trackbarWindowName = "Trackbars";
void on_trackbar(int, void*)
{//This function gets called whenever a
 // trackbar position is changed





}
string intToString(int number) {


	std::stringstream ss;
	ss << number;
	return ss.str();
}


void morphOps(Mat &thresh) {

	//create structuring element that will be used to "dilate" and "erode" image.
	//the element chosen here is a 3px by 3px rectangle

	Mat erodeElement = getStructuringElement(MORPH_RECT, Size(3, 3));
	//dilate with larger element so make sure object is nicely visible
	Mat dilateElement = getStructuringElement(MORPH_RECT, Size(8, 8));

	erode(thresh, thresh, erodeElement);
	erode(thresh, thresh, erodeElement);


	dilate(thresh, thresh, dilateElement);
	dilate(thresh, thresh, dilateElement);



}
void trackFilteredObject(Mat threshold, Mat HSV, Mat &cameraFeed) {

	//int x, y;

	Mat temp;
	threshold.copyTo(temp);
	//these two vectors needed for output of findContours
	vector< vector<Point> > contours;
	vector<Vec4i> hierarchy;
	//find contours of filtered image using openCV findContours function
	findContours(temp, contours, hierarchy, CV_RETR_CCOMP, CV_CHAIN_APPROX_SIMPLE);
	//use moments method to find our filtered object
	double refArea = 0;
	bool objectFound = false;
	if (hierarchy.size() > 0) {
		int numObjects = hierarchy.size();
		//if number of objects greater than MAX_NUM_OBJECTS we have a noisy filter
		if (numObjects<MAX_NUM_OBJECTS) {
			for (int index = 0; index >= 0; index = hierarchy[index][0]) {

				Moments moment = moments((cv::Mat)contours[index]);
				double area = moment.m00;

				//if the area is less than 20 px by 20px then it is probably just noise
				//if the area is the same as the 3/2 of the image size, probably just a bad filter
				//we only want the object with the largest area so we safe a reference area each
				//iteration and compare it to the area in the next iteration.
				if (area>MIN_OBJECT_AREA) {
					fruit apple;
					apple.setXPos(moment.m10 / area);
					apple.setYPos(moment.m01 / area);
					apples.push_back(apple);

					objectFound = true;

				}
				else objectFound = false;


			}
			//let user know you found an object
			if (objectFound == true) {
				//draw object location on screen
				//drawObject(apples, cameraFeed);
			}

		}
		else putText(cameraFeed, "TOO MUCH NOISE! ADJUST FILTER", Point(0, 50), 1, 2, Scalar(0, 0, 255), 2);
	}
}

void trackFilteredObject(fruit f, Mat threshold, Mat HSV, Mat &cameraFeed) {
		//vector<fruit> apples;
	//int x, y;

	Mat temp;
	threshold.copyTo(temp);
	//these two vectors needed for output of findContours
	vector< vector<Point> > contours;
	vector<Vec4i> hierarchy;
	//find contours of filtered image using openCV findContours function
	findContours(temp, contours, hierarchy, CV_RETR_CCOMP, CV_CHAIN_APPROX_SIMPLE);
	//use moments method to find our filtered object
	double refArea = 0;
	bool objectFound = false;
	if (hierarchy.size() > 0) {
		int numObjects = hierarchy.size();
		//if number of objects greater than MAX_NUM_OBJECTS we have a noisy filter
		if (numObjects<MAX_NUM_OBJECTS) {
			for (int index = 0; index >= 0; index = hierarchy[index][0]) {

				Moments moment = moments((cv::Mat)contours[index]);
				double area = moment.m00;

				//if the area is less than 20 px by 20px then it is probably just noise
				//if the area is the same as the 3/2 of the image size, probably just a bad filter
				//we only want the object with the largest area so we safe a reference area each
				//iteration and compare it to the area in the next iteration.
				if (area>MIN_OBJECT_AREA) {
					fruit apple;
					apple.setXPos(moment.m10 / area);
					apple.setYPos(moment.m01 / area);
					apple.setColor(f.getColor());
					apple.setType(f.getType());
					apples.push_back(apple);
					//cout << apples.size();
					objectFound = true;

				}
				else objectFound = false;


			}
			//let user know you found an object
			if (objectFound == true) {
				//draw object location on screen
				//drawObject(apples, cameraFeed);
				

			}

		}
		else putText(cameraFeed, "TOO MUCH NOISE! ADJUST FILTER", Point(0, 50), 1, 2, Scalar(0, 0, 255), 2);
	}
}

int main(int argc, char* argv[])
{
	
	//if we would like to calibrate our filter values, set to true.
	bool calibrationMode = false;

	//Matrix to store each frame of the webcam feed
	Mat cameraFeed;
	Mat threshold;
	Mat HSV;


	int fcount = 0;
	time_t start, end;
	// fps calculated using number of frames / seconds
	double fps;
	// start the clock
	time(&start);
	//while(cvGrabFrame(capture))


	//video capture object to acquire webcam feed
	VideoCapture capture;
	//open capture object at location zero (default location for webcam)
	capture.open(0);
	//set height and width of capture frame
	capture.set(CV_CAP_PROP_FRAME_WIDTH, FRAME_WIDTH);
	capture.set(CV_CAP_PROP_FRAME_HEIGHT, FRAME_HEIGHT);
	//start an infinite loop where webcam feed is copied to cameraFeed matrix
	//all of our operations will be performed within this loop
	 {
		//store image to matrix
		//capture.read(cameraFeed);
		cameraFeed=imread("images/frame.jpg");
	/*	if (fcount % 60 == 0)
		{
			
			cout << "Frame" << frame << endl;
			Mat frame;
			capture >> frame;
			cout << "frame no. " << fcount << endl;
			string path = "images/frame" + to_string(fcount) + ".jpg";
			cout << path;
			imwrite(path, frame);
		}
		fcount++;*/
		//convert frame from BGR to HSV colorspace
		cvtColor(cameraFeed, HSV, COLOR_BGR2HSV);

		if (calibrationMode == true) {
			//if in calibration mode, we track objects based on the HSV slider values.
			cvtColor(cameraFeed, HSV, COLOR_BGR2HSV);
			inRange(HSV, Scalar(H_MIN, S_MIN, V_MIN), Scalar(H_MAX, S_MAX, V_MAX), threshold);
			morphOps(threshold);
			//imshow(windowName2, threshold);
			trackFilteredObject(threshold, HSV, cameraFeed);
		}
		else {
			
			
			fruit tangerine("tangerine"), tomato("Cantaloupe"), potato("potato");
			//0, 30, 179, 256, 175, 256
			
			/*banana.setHSVmin(Scalar(0, 0, 0));
			banana.setHSVmax(Scalar(255, 255, 255));

			cherry.setHSVmin(Scalar(0, 0, 0));
			cherry.setHSVmax(Scalar(255, 255, 255));
*/

			cvtColor(cameraFeed, HSV, COLOR_BGR2HSV);
			inRange(HSV, tangerine.getHSVmin(), tangerine.getHSVmax(), threshold);
			morphOps(threshold);
			trackFilteredObject(tangerine,threshold, HSV, cameraFeed);

			cvtColor(cameraFeed, HSV, COLOR_BGR2HSV);
			inRange(HSV, tomato.getHSVmin(), tomato.getHSVmax(), threshold);
			morphOps(threshold);
			trackFilteredObject(tomato,threshold, HSV, cameraFeed);
			
			/*cvtColor(cameraFeed, HSV, COLOR_BGR2HSV);
			inRange(HSV, potato.getHSVmin(), potato.getHSVmax(), threshold);
			morphOps(threshold);
			trackFilteredObject(potato,threshold, HSV, cameraFeed);*/

			
			
		}

		//show frames 
		//imshow(windowName2,threshold);
		//cout << apples.size();
		vector<string> v;
		for (std::vector<fruit>::iterator it = apples.begin(); it != apples.end(); ++it) {
			v.push_back( (*it).getType());
		}
		ofstream outfile;// declaration of file pointer named outfile
		ifstream my_file("data.txt");
		bool saved = false;
		outfile.open("data.txt", ios::out);
		set<string> unique(v.begin(), v.end());

		//cout << "unique words " << unique.size() << endl;

		//outputs a list of uniqe words
		if (!saved) {
		for (auto element : unique){
			//cout << element << ' ' << count(v.begin(), v.end(), element) << endl;
		
			
				outfile << element << ' ' << count(v.begin(), v.end(), element)<<"\n";
			}
			saved = true;
		}
		outfile.close();
		//cin >> saved;
		//imshow(windowName, cameraFeed);
		//imshow(windowName1,HSV);


		//delay 30ms so that screen can refresh.
		//image will not appear without this waitKey() command
		waitKey(30);
	}






	return 0;
}









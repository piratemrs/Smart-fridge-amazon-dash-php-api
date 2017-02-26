#pragma once
#include<string>
#include <opencv2/opencv.hpp>
#include <opencv/cv.h>
using namespace std;
using namespace cv;
class fruit
{
public:
	fruit();
	fruit(string name);
	~fruit();
	int getXPos();
	void setXPos(int x);

	int getYPos();
	void setYPos(int y);

	Scalar getHSVmax();
	Scalar getHSVmin();

	void setHSVmax(Scalar max);
	void setHSVmin(Scalar min);

	string getType() { return Type; }
	void setType(string t) { Type = t; }

	Scalar getColor() { return Color; }
	void setColor(Scalar c) { Color = c; }
private:
	int xPos,yPos;
	string Type;
	cv::Scalar HSVmin, HSVmax,Color;

};


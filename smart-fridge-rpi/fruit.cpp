#include "fruit.h"



fruit::fruit()
{
}
fruit::fruit(string name) 
{
	setType(name);
	if (name == "tangerine")
	{
		setHSVmin(Scalar(1, 105, 71)); //1,105,71,16,226,240
		setHSVmax(Scalar(16, 226, 240));
		setColor(Scalar(0, 255, 0));
	}
	if (name == "Cantaloupe")
	{
		setHSVmin(Scalar(17, 53, 47));
		setHSVmax(Scalar(35, 190, 171));
		setColor(Scalar(0, 0, 255));
	}
	if (name == "potato")
	{
		setHSVmin(Scalar(16, 61, 161));
		setHSVmax(Scalar(25, 158, 246));
		setColor(Scalar(255, 0, 0));
	}
}

fruit::~fruit()
{
}

int fruit::getXPos() {

	return fruit::xPos;
}

void fruit::setXPos(int x) {
	fruit::xPos = x;
}

int fruit::getYPos() {

	return fruit::yPos;
}

void fruit::setYPos(int y) {
	fruit::yPos = y;
}

Scalar fruit::getHSVmax() {
	return fruit::HSVmax;
}
Scalar fruit::getHSVmin() {
	return fruit::HSVmin;

}

void fruit::setHSVmax(Scalar max) {
	fruit::HSVmax = max;
}
void fruit::setHSVmin(Scalar min) {
	fruit::HSVmin = min;

}
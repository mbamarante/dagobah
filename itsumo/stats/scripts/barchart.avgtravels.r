#!/usr/bin/Rscript --vanilla --default-packages=utils

##
## author: maicon amarante
##

matrix <- read.table('../poau/bar.avgtravels.data', header=TRUE)
err <- read.table('../poau/bar.avgtravels.error', header=TRUE)

postscript("../poau/bar.avgtravels.eps",
		width = 8.0, height = 5.0,
				  horizontal = FALSE, onefile = FALSE, paper = "special")

barx <- barplot(
	as.matrix(matrix), main="Average Number of Travels", 
	angle = 15+10*1:5, ylab= "steps", ylim= c(0,15), xlab="min travel time", beside=TRUE, 
	col=c("lightblue", "mistyrose","lightcyan", "lavender"), 
	names.arg=c("0", "100", "400", "700", "1000", "1500", "2000")
)

legend("topright", c("GNR","GR","L","L+C"), cex=1.0, bty="n", fill=c("lightblue", "mistyrose","lightcyan", "lavender"))

## ERROR BAR ##

error.bar <- function(x, y, upper, lower=upper, length=0.05,...){
	if(length(x) != length(y) | length(y) !=length(lower) | length(lower) != length(upper))
	stop("vectors must be same length")
	arrows(x,y+upper, x, y-lower, angle=90, code=3, length=length, ...)
}

error.bar(barx, c(matrix$X0,matrix$X100,matrix$X400,matrix$X700,matrix$X1000,matrix$X1500,matrix$X2000), 
								c(err$X0,err$X100,err$X400,err$X700,err$X1000,err$X1500,err$X2000))

dev.off()

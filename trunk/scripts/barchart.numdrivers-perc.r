#!/usr/bin/Rscript --vanilla --default-packages=utils

##
## author: maicon amarante
##

data=c(25000,24750,23015.8,14346.8,9521,4452.4,1788.8)
p=data*100/25000

postscript("../poau/bar.numdrivers-perc.eps",
		width = 8.0, height = 5.0,
				  horizontal = FALSE, onefile = FALSE, paper = "special")

b <- barx <- barplot(
	as.matrix(p), main="% number of drivers", 
	angle = 15+10*1:5, ylab= "percentual", ylim= c(0,110), xlab="min travel time", beside=TRUE, 
	col=c("lightblue"), space=0.2,
	names.arg=c("0", "100", "400", "700", "1000", "1500", "2000")
)

text(b, p, labels = format(p, 4),pos = 3, cex = .75)

dev.off()


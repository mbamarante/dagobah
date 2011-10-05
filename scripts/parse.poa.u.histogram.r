#!/usr/bin/Rscript --vanilla --default-packages=utils

##
## parse histogram and calculate the average.
## author: maicon amarante
##

net = "poa"
distrib = "u"
home = "../poau/"

source("kernel.histogram.r")

#print(parse("15k","gnr",10,1))
#parse("15k","gr",10,1)
#parse("15k","l",10,5)
#parse("15k","lc",10,5)

#parse("20k","gnr",1,1)
#parse("20k","gr",1,1)
#parse("20k","l",1,1)
#parse("20k","lc",1,1)

parse("25k","gnr",1,1)
parse("25k","gr",1,1)
parse("25k","l",1,1)
parse("25k","lc",1,1)

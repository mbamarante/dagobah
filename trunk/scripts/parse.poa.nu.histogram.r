#!/usr/bin/Rscript --vanilla --default-packages=utils

##
## parse histogram and calculate the average.
## author: maicon amarante
##

net = "poa"
distrib = "nu"
home = "../poanu/"

source("kernel.histogram.r")

parse("50k","gnr",1,1)
parse("50k","gr",1,1)
parse("50k","l",1,1)
parse("50k","lc",1,1)

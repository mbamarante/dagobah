## function to generate average density vector ##
generate_average_density <- function(SensorData)
	{
		density=0 # soma das densidades
		time=0 # em qtos steps houveram veiculos na laneset
		steps=0 # total de steps da simulacao (gravados no sensor)

		for (i in 2:length(SensorData)) { # percorre colunas (uma coluna para cada lanes)
			density[i-1]=0
			time[i-1]=length(SensorData[,i])
			steps=length(SensorData[,i])

			used_steps=0;

			for (j in 1:length(SensorData[,i])) { # percorre linhas (uma linha para cada steps)

				#if (j > steps*0.2 && j < steps-(steps*0.2) ) { # ignorar x% dos steps iniciais e finais
				if (j > steps*0.4 && j < steps-(steps*0.4)) {
					used_steps=used_steps+1
					density[i-1]=density[i-1]+SensorData[j,i]
					if (SensorData[j,i] == 0) time[i-1]=time[i-1]-1
				} else {					
					time[i-1]=time[i-1]-1
				}
					

			}
		}

		time_n=0

		# time "normalizado" pelo total de steps da simulacao
		for (i in 1:length(time)) {
			#time_n[i]=time[i]/steps
			time_n[i]=time[i]/used_steps
			print(time_n[i])
		}

		# densidade media enquanto a laneset esteve ocupada
		density_avg=0

		for (i in 1:length(density)) {
			if (time[i]==0) div=1 else div=time[i]
			density_avg[i]=density[i]/div
		}

		# trick: limitando o valor minimo da densidade média para a escala ir de 0 a 10
		#for (i in 1:length(density_avg)) {
		#	print(density_avg[i])
		#	if (density_avg[i] < 0.1 && density_avg[i] != 0) density_avg[i]=0.1 else 0
		#}


		# index: tempo normalizado dividido pela densidade media
		index=0

		for (i in 1:length(density_avg)) {
			if (density_avg[i]==0) div=1 else div=density_avg[i]
			index[i]=time_n[i]/div
		}

		# mediana: para termos um escalar que tenha algum significado
		# median_ = median(index)

		#png("laneset_densities_sensor.png")
		#hist(index, main=paste("Histogram of Network Occupation Index - R - median = ", round(median_,3)))

		do = density/time
		dt = density/steps
		dm = (do+dt)/2

		#median_ = median(density_avg)
		#hist(density_avg, main=paste("Histogram of Network Occupation Index - R - median = ", round(median_,3)), ylim=c(0,15), xlim=c(0,1))
		#hist(dm, main=paste("Histogram of Network Occupation Index - R - median = ", round(median_,3)), ylim=c(0,15), xlim=c(0,1))

		density_avg

	}

		

parse <- function(demand, variant, rounds, episodes) # variant = {gnr|gr|l|lc}
	{
		average_density = 0;

		for (i in 1:rounds) { # iterate rounds

			for (j in 1:episodes) { # iterate episodes

				filename = ""

				if (episodes>1) filename = paste(home,"sensor.",net,".",distrib,"_",demand,".",variant,".laneset_densities_sensor-round_",i,"-episode_",j,".txt", sep = "")
				else filename = paste(home,"sensor.",net,".",distrib,"_",demand,".",variant,".laneset_densities_sensor.txt", sep = "")

				# somando medias de ocupacao por link para calcular a media geral dos experimentos
				average_density = average_density + generate_average_density(read.table(filename, header=TRUE))
				#print(paste("round = ", i, "; episodes=", j, " = ", average_density))
	
			}
		}

		# media geral dos experimentos
		average_density = average_density/(rounds*episodes)
		## print(paste("average = ", average_density))

		average_density

		# imprimir histograma
		postscript(paste(home,"histogram.",net,".",distrib,"_",demand,".",variant,".eps", sep = ""),
				width = 8.0, height = 5.0,
						  horizontal = FALSE, onefile = FALSE, paper = "special")

		hist(	average_density, 
			## main=paste("Histograma de Ocupação da Rede - mediana = ", round(median_,3)), 
			main=paste(""),
			xlab="densidade de ocupação (%)",
			ylab="frequência (número de links)",
			ylim=c(0,120),
			xlim=c(0,1)
			)

		dev.off()

		print(paste("file ",home,"histogram.",net,".",distrib,"_",demand,".",variant,".eps: DONE!", sep = ""))

  }

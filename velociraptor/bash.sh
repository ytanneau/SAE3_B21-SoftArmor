#gcc raptor.c -o raptor -Wall

#gcc -c fonction.c -lmysqlclient
#gcc -c raptor.c -lmysqlclient
#gcc raptor.o fonction.o -o programme

#gcc raptor.c fonction.c  -lmysqlclient -o programme

gcc raptor.c fonction.c -o prog `mariadb_config --cflags --libs`

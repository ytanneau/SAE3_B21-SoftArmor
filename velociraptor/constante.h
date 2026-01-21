#ifndef CONSTANTE_H
#define CONSTANTE_H


#include <fcntl.h>
#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <errno.h>
#include <stdbool.h>
#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <unistd.h>
#include <signal.h>
#include <sys/wait.h>
#include <time.h>
#include <mariadb/mysql.h>

//-------------------------------------------------------

#define SERVER "[RAPTOR]"
#define DEBUG true


#define BDD_HOST "saedb"
#define BDD_USER "saedb"
#define BDD_PASSWORD "dbsae3dunyles"
#define BDD_NAME "saedb"
#define BDD_PORT 8080


#define ERREUR_INSTRUCTION 0
#define ERREUR_ACCES 1
#define ERREUR_NEW_COLIS 2
#define ERREUR_COLIS_INEXISTENT 3
#define ERREUR_PHOTO_INEXISTENT 4


#define INSTRUCTION_DELIMITER "."
#define INSTRUCTION_FIN -1
#define INSTRUCTION_CONNECTION 1
#define INSTRUCTION_NEW_COLIS 2
#define INSTRUCTION_INFO_COLIS 3
#define INSTRUCTION_PHOTO_COLIS 4


#define ERREUR "ERROR"
#define CONNECTION "CONNECT"
#define COLIS "COLIS"
#define ETAPE "STAPE"
#define MODE "REMISE"
#define CAUSE "RAISON"


#define CHEMAIN 1
#define NB_COLIS 2
#define OPTION 3


#define TAILLE 100
#define TRAME_TAILLE 400


#define DELIMITER "="
#define MDP_TAILLE 33


#define BORDEREAU_SIZE 13
#define BORDEREAU_CARACTERE "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"


#endif

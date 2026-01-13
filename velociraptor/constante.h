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

#define BDD true
#define BDD_HOST "10.253.5.107"
#define BDD_USER "sae"
#define BDD_PASSWORD "dbsae3dunyles"
#define BDD_NAME "saedb"
#define BDD_PORT 3306


#define TABLE "_colis"
#define VIEW "nb_colis_non_livres"
#define COLON_ETAPE "etape"
#define COLON_MODE "mode"
#define COLON_RAISON "raison_refus"


#define VALUE_ETAPE_FIN 9
#define VALUE_MODE_ABSENT 1
#define VALUE_MODE_REFU 2


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
#define ETAPE "ETAPE"
#define MODE "REMISE"
#define CAUSE "RAISON"
#define VIDE "N/A"
#define PHOTO "PHOTO"

#define CHEMAIN 1
#define NB_COLIS 2
#define OPTION 3


#define TAILLE 100
#define TAILLE_SQL 200
#define TRAME_TAILLE 400


#define DELIMITER "="
#define MDP_TAILLE 33


#define BORDEREAU_SIZE 13
#define BORDEREAU_CARACTERE "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"

#define TAILLE_PHOTO 62
#define FICHIER_PHOTO "carton_endommage.png"

#endif

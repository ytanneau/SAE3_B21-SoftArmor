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

// base
#define SERVER "[RAPTOR]"
#define DEBUG true

// bdd
#define BDD true

// element bdd
#define TABLE "_colis"
#define VIEW "nb_colis_non_livres"
#define COLON_ETAPE "etape"
#define COLON_MODE "mode"
#define COLON_RAISON "raison_refus"
#define COLON_DATE "date_update"

// valeur clé
#define VALUE_ETAPE_FIN 9
#define VALUE_MODE_ABSENT 1
#define VALUE_MODE_REFU 2

// valeur erreur
#define ERREUR_INSTRUCTION 0
#define ERREUR_ACCES 1
#define ERREUR_NEW_COLIS 2
#define ERREUR_COLIS_INEXISTENT 3
#define ERREUR_PHOTO_INEXISTENT 4

// valeur intruction
#define INSTRUCTION_DELIMITER "."
#define INSTRUCTION_FIN -1
#define INSTRUCTION_CONNECTION 1
#define INSTRUCTION_NEW_COLIS 2
#define INSTRUCTION_INFO_COLIS 3
#define INSTRUCTION_PHOTO_COLIS 4

// reponse mot clé
#define ERREUR "ERROR"
#define CONNECTION "CONNECT"
#define COLIS "COLIS"
#define ETAPE "ETAPE"
#define MODE "REMISE"
#define CAUSE "RAISON"
#define VIDE "N/A"
#define PHOTO "PHOTO"
#define DATE "DATE"

// parametre / option
#define CHEMAIN 1
#define NB_COLIS 2
#define PORT 3
#define OPTION 4

// taille des chaine
#define TAILLE 100
#define TAILLE_SQL 200
#define TRAME_TAILLE 400

// element du login
#define DELIMITER "="
#define MDP_TAILLE 33

// element du bordereau
#define BORDEREAU_SIZE 13
#define BORDEREAU_CARACTERE "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"

// element de photo
#define TAILLE_PHOTO 62
#define FICHIER_PHOTO "carton_endommage.png"

#endif

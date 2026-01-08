#include "constante.h"
#ifndef FONCTION_H
#define FONCTION_H

typedef struct compte
{
    char id[TAILLE];
    char mdp[TAILLE];
    struct compte *next;
} compte;

compte* init_compte(const char* chemain);
void affiche_compte(compte* c);

void comminication(int cnx, compte* c, bool colisInfinit, int nbColisMax, MYSQL *conn);
void fin(int cnx);
void tombe(int sig);

void envoier_message(int cnx, char *message);
void message_erreur(int cnx, int valeur);

bool authtification(compte* c, char buff[TAILLE]);
void connection(int cnx, bool connect);

void new_colis(int cnx, bool colisInfinit, int nbColisMax);
void genere_code(char *code);
int colis_encour();


#endif
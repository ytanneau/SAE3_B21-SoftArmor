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

void comminication(int cnx, compte* c, bool colisInfinit, int nbColisMax, MYSQL* conn);
void fin(int cnx);
void tombe(int sig);

void envoier_message(int cnx, char *message);
void message_erreur(int cnx, int valeur);
void envoier_code(int cnx, char *message);

bool authtification(compte* c, char buff[TAILLE]);
void connection(int cnx, bool connect);

void new_colis(int cnx, bool colisInfinit, int nbColisMax, MYSQL* conn);
void genere_code(char *code);
int colis_encour(MYSQL* conn,int cnx);
bool colis_existe(MYSQL *conn, int cnx, char *code);

void info_colis(int cnx, char* code, MYSQL* conn);
bool check_code(char* code);

void photo(int cnx, char* code, MYSQL* conn);
void envoier_photo(int cnx, char *fichier);
void encode_photo(char *src, char *des);

#endif
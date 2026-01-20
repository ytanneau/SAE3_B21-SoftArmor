#include "constante.h"
#ifndef FONCTION_H
#define FONCTION_H

typedef struct COMPTE
{
    char id[TAILLE];
    char mdp[TAILLE];
    struct COMPTE *next;
} COMPTE;

typedef struct SESSION
{
    MYSQL *conn;
    FILE *log;
    char client_ip[INET_ADDRSTRLEN];
    int cnx;
    char login[TAILLE];
    bool debug;
    bool bdd;
} SESSION;


COMPTE* init_compte(const char* chemain, FILE *logI);
void affiche_compte(COMPTE* c, FILE *logI);
void init_bdd(MYSQL *conn, FILE *logI);

void comminication(SESSION *data, COMPTE* c, bool colisInfinit, int nbColisMax);
void fin(SESSION *data);
void tombe(int sig);

void envoier_message(SESSION *data, char *message);
void message_erreur(SESSION *data, int valeur);
void envoier_code(SESSION *data, char *message);

bool authtification(SESSION *data,COMPTE* c);
void connection(SESSION *data, bool connect);

void new_colis(SESSION *data, bool colisInfinit, int nbColisMax);
void genere_code(SESSION *data, char *code);
int colis_encour(SESSION *data);
bool colis_existe(SESSION *data, char *code);

void info_colis(SESSION *data);
bool check_code(char* code);

void photo(SESSION *data);
void envoier_photo(SESSION *data, char *fichier);
void encode_photo(char *src, char *des);
void chaine_en_binaire(const char src, char *dest);

void log_line(SESSION *data, char *msg);
void log_transforme(char *str);
void log_init(FILE *fd, char *message);
void help();

#endif
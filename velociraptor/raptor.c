#include "constante.h"
#include "fonction.h"

int main(int argc, char const *argv[])
{
    // initalisation des variable
    bool colisInfinit = false;
    int pid;
    int sock;
    int opt = 1;
    int ret;
    struct sockaddr_in addr;
    struct sockaddr_in conn_addr;
    compte *c = NULL;
    int size = sizeof(conn_addr);
    int cnx; // le file descriptor du sock
    MYSQL *conn;

    srand(time(NULL)); // aléatoire du borderau
    signal(SIGCHLD, tombe); // eviter les enfant zombie
    printf("%s START\n", SERVER);


    // verifie que le nombre de parametre est se lui attendu
    if (argc != OPTION)
    {
        fprintf(stderr, "[FATAL] Pas le bon nombre d'option.\n");
        exit(EXIT_FAILURE);
    }


    // recupération des compte
    c = init_compte(argv[CHEMAIN]);
    printf("%s SUCCESS INIT COMPTE\n", SERVER);
    if (DEBUG)
    {
        printf("[DEBUG] COMPTE :\n");
        affiche_compte(c);
    }


    // verifie que le nombre de colie est bon
    int nbColisMax = atoi(argv[NB_COLIS]);
    if (nbColisMax == 0 || nbColisMax < -1)
    {
        fprintf(stderr, "[FATAL] Nombre de colis incorecte.\n");
        exit(EXIT_FAILURE);
    }
    else if (nbColisMax == -1)
    {
        colisInfinit = true;
        printf("%s SUCCESS COLIS SET INFINIT\n", SERVER);
    }
    else
    {
        printf("%s SUCCESS COLIS SET %d\n", SERVER, nbColisMax);
    }

    // inisalisation avec la bdd
    conn = mysql_init(NULL);
    if(BDD)
    {
        init_bdd(conn);
    }
    

    
    
    

    // mise en place du socket
    sock = socket(AF_INET, SOCK_STREAM, 0);
    addr.sin_addr.s_addr = inet_addr("0.0.0.0"); //"127.0.0.1"
    addr.sin_family = AF_INET;
    addr.sin_port = htons(atoi(argv[PORT]));
    ret = bind(sock, (struct sockaddr *)&addr, sizeof(addr));


    if (setsockopt(sock, SOL_SOCKET, SO_REUSEADDR, &opt, sizeof(opt)) == -1) {
        fprintf(stderr, "[ERROR] setsockopt.\n");
        exit(EXIT_FAILURE);
    }
    printf("[RAPTOR] SUCCESS INIT SOCKET\n");


    // boucle que mode server
    while (true)
    {
        // a temps une demande
        if(listen(sock, 1) == -1)
        {
            printf("%s ERROR : %d", SERVER, errno);
        }
        else
        {
            // le file descriptor du sock
            cnx = accept(sock, (struct sockaddr *)&conn_addr, (socklen_t *)&size);
            if (cnx == -1)
            {
                printf("%s ERROR : %d", SERVER, errno);
            }
            else
            {
                pid = fork();
                if (pid == -1)
                {
                    printf("%s ERROR : %d", SERVER, errno);
                }
                else if (pid == 0) // fils
                {
                    close(sock);
                    comminication(cnx, c, colisInfinit, nbColisMax, conn);
                }
            }
            
        }
    }
    
    
    return EXIT_SUCCESS;
}


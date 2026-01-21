#include "constante.h"
#include "fonction.h"




int main(int argc, char *argv[])
{
    // initalisation des variable
    char *chemain = DEFAULT_LOGIN;
    bool colisInfinit = false;
    int port = DEFAULT_PORT, nbColisMax = DEFAULT_COLIS;
    char *portC = NULL, *nbColisMaxC = NULL;

    char message[TAILLE];
    int pid;
    int sock;
    int opt;
    int ret;
    struct sockaddr_in addr;
    struct sockaddr_in conn_addr;
    COMPTE *c = NULL;
    int size = sizeof(conn_addr);
    int cnx; // le file descriptor du sock
    MYSQL *conn;
    SESSION data;
    strcpy(data.login, VIDE);
    data.bdd = BDD;
    data.debug = DEBUG;

//-------------------------------------------------------

    //oberture des fichier
    FILE *logI = fopen(INIT_FILE, "w");
    FILE *logC = fopen(LOG_FILE, "w");

    // permer lécriture en directre
    setvbuf(logI, NULL, _IONBF, 0);
    setvbuf(logC, NULL, _IONBF, 0);
    data.log = logC;
    
//-------------------------------------------------------

    //defénition des parametre attendu
    struct option long_options[] = 
    { 
        {"help", no_argument, 0, 'h'}, 
        {"account", required_argument, 0, 'a'}, 
        {"nbcolis", required_argument, 0, 'n'}, 
        {"port", required_argument, 0, 'p'},
        {"bdd", no_argument, 0, 'b'}, 
        {"debug", no_argument, 0, 'd'}, 
        {0, 0, 0, 0} 
    };

    // recupération des parametre
    while ((opt = getopt_long(argc, argv, "ha:n:p:bd", long_options, NULL)) != -1)
    { 
        switch (opt) 
        { 
            case 'h': 
                log_init(logI, "[PARAMETRE] -h");
                fclose(logI);
                fclose(logC);
                help();
                break; 
                
            case 'a': 
                chemain = optarg;
                sprintf(message, "[PARAMETRE] -a : %s", chemain);
                log_init(logI, message);
                break; 
            
            case 'n':
                nbColisMaxC = optarg;
                sprintf(message, "[PARAMETRE] -n : %s\n", nbColisMaxC);
                log_init(logI, message);
                break;

            case 'p':
                portC = optarg;
                sprintf(message, "[PARAMETRE] -p : %s\n", portC);
                log_init(logI, message);
                break;

            case 'b':
                data.bdd = false;
                log_init(logI, "[PARAMETRE] -b");
                break; 

            case 'd':
                data.debug = true;
                log_init(logI, "[PARAMETRE] -d");
                break; 
                
            case '?':
                sprintf(message, "[FATAL] Option inconnue: -%c", optopt);
                log_init(logI, message); 
                exit(EXIT_FAILURE);
                break;
        } 
    }

//---------------------------------------------------------------------------------------------
    
    srand(time(NULL)); // aléatoire du borderau
    signal(SIGCHLD, tombe); // eviter les enfant zombie
    log_init(logI, "START");

//---------


    // recupération des compte
    c = init_compte(chemain, logI);
    log_init(logI, "SUCCESS INIT COMPTE");
    if (data.debug)
    {
        fprintf(logI, "[DEBUG] COMPTE :\n");
        affiche_compte(c, logI);
    }

//---------

    // inisalisation avec la bdd
    conn = mysql_init(NULL);
    if(data.bdd)
    {
        init_bdd(conn, logI);
        data.conn = conn;
    }

//-------------------------------------------------------


    // recupère la valeur en paramètre
    if (nbColisMaxC != NULL)
    {
        nbColisMax = atoi(nbColisMaxC);
    }

    // verifie que le nombre de colie est bon
    if (nbColisMax == 0 || nbColisMax < -1)
    {
        log_init(logI, "[FATAL] Nombre de colis incorecte.");
        exit(EXIT_FAILURE);
    }
    else if (nbColisMax == -1)
    {
        colisInfinit = true;
        log_init(logI, "SUCCESS COLIS SET INFINIT");
    }
    else
    {
        sprintf(message, "SUCCESS COLIS SET %d", nbColisMax);
        log_init(logI, message);
    }

//---------
    
    //recupère la valeur en paramètre
    if (portC != NULL)
    {
        port = atoi(portC);
    }

    // verifie que port est bon
    if (port <= 0)
    {
        log_init(logI, "[FATAL] Port incorecte.");
        exit(EXIT_FAILURE);
    }
    else
    {
        sprintf(message, "SUCCESS PORT SET %d", port);
        log_init(logI, message);
    }

//-------------------------------------------------------


    // mise en place du socket
    sock = socket(AF_INET, SOCK_STREAM, 0);
    addr.sin_addr.s_addr = inet_addr(IP); 
    addr.sin_family = AF_INET;
    addr.sin_port = htons(port);
    ret = bind(sock, (struct sockaddr *)&addr, sizeof(addr));

    opt = 1;
    if (setsockopt(sock, SOL_SOCKET, SO_REUSEADDR, &opt, sizeof(opt)) == -1) 
    {
        log_init(logI, "[ERROR] setsockopt.");
        exit(EXIT_FAILURE);
    }
    log_init(logI,"SUCCESS INIT SOCKET");

    log_init(logI, "READY");
    fclose(logI);

//---------------------------------------------------------------------------------------------

    // boucle mode server
    while (true)
    {
        // a temps une demande
        if(listen(sock, 1) == -1)
        {
            fprintf(data.log, "[ERROR] : %d", errno);
        }
        else
        {
            // le file descriptor du sock
            cnx = accept(sock, (struct sockaddr *)&conn_addr, (socklen_t *)&size);
            if (cnx == -1)
            {
                fprintf(data.log, "[ERROR] : %d", errno);
            }
            else
            {
                pid = fork();
                if (pid == -1)
                {
                    fprintf(data.log, "[ERROR] : %d", errno);
                }
                else if (pid == 0) // fils
                {
                    SESSION encour = data; //data est la structure de base, et son crée en cour pour évier des problème avec pointeur
                    char client_ip[INET_ADDRSTRLEN]; 
                    inet_ntop(AF_INET, &conn_addr.sin_addr, client_ip, sizeof(client_ip)); //recupération de ip
                    strcpy(encour.client_ip , client_ip);
                    encour.cnx = cnx;

                    close(sock);
                    
                    comminication(&encour, c, colisInfinit, nbColisMax);
                    fin(&encour); // est pas sortitre de comminication, mais au cas ou
                }
            }
            
        }
    }
    
    
    return EXIT_SUCCESS;
}


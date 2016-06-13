/* 
 * This file handles the new transactions done by uploading a csv file with the following format:
 * sourceName,targetName,TAN,Amount
 * sourceName && targetName must be unique usernames
 * Return values are 255: too less arguments
 *                   254: can't open file
 *                   253: can't connect to database
 *                   1-252: how many erros happened in transaction file
 * */


#include <stdio.h>
#include <stdlib.h>
#include <ctype.h>
#include <string.h>
#include <mysql.h>
#define ARRAY_LENGTH 1024

int main (int argc, char **argv)
{
    // file read
    char c;
    char *input = argv[1];
    char buffer[ARRAY_LENGTH];
    char sourceTAN[ARRAY_LENGTH];
    char sourceName[ARRAY_LENGTH];
    char targetName[ARRAY_LENGTH];
    int sourceID,targetID,desiredAmount, availableAmount;
    int ret=0;
    FILE *input_file = NULL;
    // mysql
    MYSQL *conn;
    MYSQL_RES *res;
    MYSQL_ROW row;
    char *server = "localhost";
    char *user = "root";
    char *password = "kjvl4lvn";
    char *database = "securecoding";
    conn = mysql_init(NULL);

    if (argc < 2) // check if file is specified
    {
        fprintf(stderr, "Usage: %s filename\n",argv[0]);
        exit(255);
    }

    // try open file
    input_file = fopen(input, "r");    
    if (input_file == 0)
    {
     	printf("Could not read input file!");
        exit(254);
    }
    
    // try to connect to database
    if (mysql_real_connect(conn, server, user, password ,database, 0, NULL, 0) == NULL) 
    {
        fprintf(stderr, "%s\n", mysql_error(conn));
        mysql_close(conn);
        exit(253);
    }  

    // read line by line from file
    while (fscanf(input_file,"%1024[^,],%1024[^,],%1024[^,],%d\n",sourceName,targetName,sourceTAN,&desiredAmount) == 4) {
        //printf("%s %s %s %d\n",sourceName,targetName,sourceTAN,desiredAmount);
        // here mysql code
        int isSourceIDvalid = 0,isTargetIDvalid = 0, isTANvalid = 0, isAmountvalid = 0;
        
        // check if users exist and credit-worthiness and valid TAN
        sprintf(buffer, "SELECT * FROM users where uname = '%s';", sourceName);
       // printf("Kontrollpunkt 1\n");
        mysql_query(conn, buffer);
        res = mysql_store_result(conn);   
      // printf("buffer: %s\n",buffer);
        if (mysql_num_rows(res) > 0) {
            isSourceIDvalid = 1;
            //printf("SourceID found.\n");
            
            // user exists! now check for money
            row = mysql_fetch_row(res);
            sscanf(row[0], "%d", &sourceID);
            sscanf(row[6], "%d", &availableAmount);
            
            if ( desiredAmount <= availableAmount ) {
                isAmountvalid = 1;
                //printf("Enough money is available.\n");
            }
        }
        
        // check for correct TAN.
        sprintf(buffer, "SELECT uid, value FROM tans WHERE uid=%d and value='%s'", sourceID, sourceTAN);
       //printf("Kontrollpunkt 2\n");
        mysql_query(conn, buffer);
        res = mysql_store_result(conn);       
        printf("buffer: %s\n",buffer);
        if (mysql_num_rows(res) > 0) {
            printf("TAN is valid\n");
            isTANvalid = 1;
        }
        
        //printf("Kontrollpunkt 3\n");
        // check if targetID exists
        sprintf(buffer, "SELECT * FROM users where uname = '%s';", targetName);
        mysql_query(conn, buffer);
        res = mysql_store_result(conn); 
        //printf("buffer: %s\n",buffer);
        if (mysql_num_rows(res) > 0) {
            isTargetIDvalid = 1;
            row = mysql_fetch_row(res);
            sscanf(row[0], "%d", &targetID);
          // printf("TargetID found.\n");
        }
        
       printf("%d %d %d %d\n",isSourceIDvalid,isTargetIDvalid,isTANvalid,isAmountvalid);
        
        //printf("Kontrollpunkt 4\n");
        // transaction is done here
        if (isSourceIDvalid && isTargetIDvalid && isTANvalid && isAmountvalid) {
            //printf("Kontrollpunkt 4.1\n");
            //printf("Start transactions");
            int approved = 1;
            if (desiredAmount > 10000)
				approved = 0;
            
            sprintf(buffer, "INSERT INTO transactions (sourceId, targetId, tan, date, amount,approved) VALUES (%d,%d,'%s',CURDATE(),%d,%d)", sourceID, targetID, sourceTAN, desiredAmount, approved);
            mysql_query(conn, buffer);
            res = mysql_store_result(conn);
            sprintf(buffer, "DELETE FROM tans WHERE uid=%d AND value='%s'", sourceID, sourceTAN);
		
            mysql_query(conn, buffer);
            res = mysql_store_result(conn);
	    if (desiredAmount>0 && approved) {
		    sprintf(buffer, "UPDATE users SET balance=balance+%d WHERE uid=%d", desiredAmount, targetID);
	  	    mysql_query(conn, buffer);

		    sprintf(buffer, "UPDATE users SET balance=balance-%d WHERE uid=%d", desiredAmount, sourceID);
	  	    mysql_query(conn, buffer);
            }

        }
        else {
            ret += 1;
        }
        
    }
    
    mysql_free_result(res);
    mysql_close(conn);
    fclose(input_file);
    //printf("\n");
    return ret;
}

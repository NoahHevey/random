#include <mysql.h>
#include <cstdio>
#include <cstdlib>
#include <cstring>
#include <iostream>

int main()
{
	const char *host = "courses";
	const char *user = "z1799146";
	const char *password = "1996Feb20";
	const char *database = "z1799146";
	const char *socket = NULL;
	unsigned int port = 0;
	unsigned int flags = 0;

	// MySQL library initialization
	if (mysql_library_init(0, NULL, NULL))
	{
		fprintf(stderr, "mysql_library_init() failed\n");
		exit(1);
	}

	MYSQL *conn;                // pointer to a MySQL connection object
	MYSQL_RES *result;          // pointer to data structure that holds query result
	MYSQL_ROW row;              // contains data about a single row from result set

	// query that returns flight number, origination, destination, miles, for each flight with passengers
	// also returns first and last name for each passenger
	const char *flightQuery = "SELECT flight.flightnum, origination, destination, miles, lastName, firstName FROM flight, passenger, manifest WHERE flight.flightnum = manifest.flightnum AND passenger.passnum = manifest.passnum";

	// initializing a connection object
	conn = mysql_init(NULL);
	if (conn == NULL)
	{
		fprintf (stderr, "mysql_init() failed\n");
		exit (1);
	}

	// establishing a database connection
	if (mysql_real_connect(conn, host, user, password, database, port, socket, flags) == NULL)
	{
		// calls mysql_errno() to display error number
		// calls mysql_error() to display a string describing error
		fprintf(stderr, "mysql_real_connect() failed:\nError %u (%s)\n", mysql_errno(conn), mysql_error(conn));
	}

	// run query
	if (mysql_query(conn, flightQuery))
	{
		fprintf(stderr, "mysql_query() failed:\n %u (%s)\n", mysql_errno(conn), mysql_error(conn));
	}
	else  // if successfull
	{
		// download all rows of result set and store in MYSQL_RES data structure
		result = mysql_store_result(conn);
		if (result == NULL)
		{
			fprintf(stderr, "mysql_store_result() failed:\n %u (%s)\n", mysql_errno(conn), mysql_error(conn));
		}
		else // if successfull display data row by row
		{
			int count = 0;
			char flightnum[10] = "0";
			while ((row = mysql_fetch_row(result)))  // retrieves a single row at a time
			{
				char currentnum[10];
				strcpy(currentnum, row[0]);
				if ((strcmp(flightnum, currentnum)) != 0)
				{
					if (count != 0)
					{
						std::cout << "count" << count;
						std::cout << std::endl;
					}
					count = 0;

					for (size_t i = 0; i < mysql_num_fields(result); i++) // print each field of row
					{
						if (i > 0)
						{
							fputc('\t', stdout);
						}

						if (i == 4)
						{
							fputc('\n', stdout);
						}
						printf("%s", row[i] != NULL ? row[i] : "NULL");
					}
				}
				else
				{
					printf("%s", row[4]);
					fputc('\t', stdout);
					printf("%s", row[5]);
				}
				count++;
				strcpy(flightnum, currentnum);
				fputc('\n', stdout);
			}

			if ((mysql_errno(conn)))
			{
				fprintf(stderr, "mysql_fetch_row() failed:\n %u (%s)\n", mysql_errno(conn), mysql_error(conn));
			}

			// free memory allocated to result set
			mysql_free_result(result);
		}
	}

	// close database connection
	mysql_close(conn);

	// library deinitialization
	mysql_library_end();

	exit(0);
}

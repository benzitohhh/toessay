from random import shuffle

issues = [
    [30, 33] + range(40, 70),
    range(70, 100),
    range(100, 130),
    range(130, 160)
]

for issue in issues:
    shuffle(issue)
    # rank the top 13
    for i in range(1, 14):
        sql = "INSERT INTO `wp_postmeta` \n\
               (`post_id`       ,`meta_key`,`meta_value`) \n\
               VALUES \n\
               (" + str(issue[i]) + ", 'rank'   , '" + str(i) + "');"
        print sql
    print "\n"




        







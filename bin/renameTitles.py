import random

posts = range(40, 159)
maxWordsPerTitle = 3

words = [line.strip() for line in open('/usr/share/dict/words')]


# for each post
for j in posts:
    titleWords = [random.choice(words) for x in range(random.randrange(1, maxWordsPerTitle+1))]
    title = (" ").join(titleWords)
    title = title[:1].upper() + title[1:]
    name = ("-").join(titleWords)
    sql = "UPDATE wp_posts \n\
          SET post_title='" + title + "', post_name='" + name + "' \n\
          WHERE id=" + str(j) + ";"
    print sql
    print "\n\n\n"





mysql -u root -p -e "Select id,status,progress from Jobs where status='processing' and progress < 1 order by ID DESC LIMIT 15;" allen_transcoder

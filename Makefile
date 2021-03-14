debug:
	heroku config:set APP_DEBUG=true LOG_LEVEL=debug

prod:
	heroku config:set APP_DEBUG=false LOG_LEVEL=info

monolog:
  channels: ['mailer']
  handlers:
    main:
      type:  rotating_file
      max_files: 10
      level: error
      handler:      nested
      formatter: log_formatter
when@prod:
  monolog:
    handlers:
      mailer:
        level:    debug
        type:       rotating_file
        max_files:  40
        path:     '%kernel.logs_dir%/mailer.prod.log'
        channels: [mailer]
        formatter: log_formatter
when@dev:
    monolog:
        handlers:
            # ...
            console:
                type:   console
                process_psr_3_messages: false
                channels: ['!event', '!doctrine', '!console']

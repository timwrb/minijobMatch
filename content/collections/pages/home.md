---
id: d74062f1-3371-44d7-a463-196d82884c92
blueprint: page
title: minijobMatch
section:
  -
    id: mg8ad6af
    padding_y: true
    blocks:
      -
        id: mg8adaj5
        test: test
        type: hero_section
        enabled: true
    type: content_blank
    enabled: true
  -
    id: mgd5kj91
    roundings:
      - tl
      - br
    padding_y: true
    bg: white
    width: 7xl
    soft_rounded_corners: true
    blocks:
      -
        id: mgd5kxs2
        image:
          - mockup-stellenanzeige.png
        bard:
          -
            type: heading
            attrs:
              textAlign: null
              level: 2
            content:
              -
                type: text
                marks:
                  -
                    type: bold
                text: 'Ihre Stelle, unsere Talente – in Minuten veröffentlicht'
          -
            type: paragraph
            attrs:
              textAlign: null
            content:
              -
                type: text
                text: 'Vorteile für Arbeitgeber:'
          -
            type: set
            attrs:
              id: mgd5mkec
              values:
                type: bullet_points
                bullet_points:
                  - 'Einfache Erstellung von Stellenanzeigen'
                  - 'Zielgenaue Ansprache von Schülern und Studenten'
                  - 'Steigerung Ihrer Sichtbarkeit bei der jungen Zielgruppe'
          -
            type: set
            attrs:
              id: mgd5l5on
              values:
                type: cta_button
                buttons:
                  -
                    id: mgd5rq5k
                    text: 'Stellenanzeige schalteb'
                    bg: primary
                    link: '#'
                    icon: suffix
                    heroicon_string: s-arrow-right
                    type: neues_set
                    enabled: true
        type: content_image
        enabled: true
    type: content_rounding
    enabled: true
updated_by: 8a94dbab-8a70-4c24-9088-a0fbe71999a9
updated_at: 1759636131
---

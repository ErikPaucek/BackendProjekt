import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useConferenceStore = defineStore('conferences', () => {
  const conferences = ref([
    {
      id: 1,
      year: '2025',
      title: 'Konferencia 2025',
      pages: [
        { id: 101, title: 'Úvod', content: '<p>Toto je úvodná stránka pre ročník 2025.</p>', attachments: [] },
        { id: 102, title: 'Program', content: '<p>Program konferencie 2025…</p>', attachments: [{ name: 'program.pdf', url: '/uploads/program2025.pdf' }] }
      ]
    },
    {
      id: 2,
      year: '2024',
      title: 'Konferencia 2024',
      pages: [
        { id: 201, title: 'Úvod', content: '<p>Archív úvodu 2024.</p>', attachments: [] },
        { id: 202, title: 'Zborník', content: '<p><a href="/uploads/zbornik2024.pdf">PDF zborník</a></p>', attachments: [{ name: 'zbornik2024.pdf', url: '/uploads/zbornik2024.pdf' }] }
      ]
    }
  ])

  function addConference(year: string, title: string) {
    const maxId = conferences.value.reduce((max, c) => c.id > max ? c.id : max, 0)
    conferences.value.push({
      id: maxId + 1,
      year,
      title,
      pages: []
    })
  }

  function addPage(confId: number, pageTitle: string) {
    const conference = conferences.value.find(c => c.id === confId)
    if (!conference) return

    const maxPageId = conference.pages.reduce((max, p) => p.id > max ? p.id : max, 0)
    conference.pages.push({
      id: maxPageId + 1,
      title: pageTitle,
      content: '',
      attachments: []
    })
  }

  function getPageById(pageId: number) {
    for (const conf of conferences.value) {
      const page = conf.pages.find(p => p.id === pageId)
      if (page) return page
    }
    return null
  }

  return { conferences, addConference, addPage, getPageById }
})

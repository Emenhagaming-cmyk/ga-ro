import { school } from "./school.js"
import { jurusan } from "./jurusan.js"
import { ppdb } from "./ppdb.js"
import { faq } from "./faq.js"
import { kontak } from "./kontak.js"
import { tataTertib } from "./tataTertib.js"

export function getKnowledge(message){

  const text = message.toLowerCase()

  let knowledge = school

  if(
    text.includes("jurusan") ||
    text.includes("rpl") ||
    text.includes("tkj") ||
    text.includes("akl")
  ){
    knowledge += "\n\n" + jurusan
  }

  if(
    text.includes("ppdb") ||
    text.includes("pendaftaran") ||
    text.includes("biaya")
  ){
    knowledge += "\n\n" + ppdb
  }

  if(
    text.includes("alamat") ||
    text.includes("telepon") ||
    text.includes("email") ||
    text.includes("kontak")
  ){
    knowledge += "\n\n" + kontak
  }

  if(
    text.includes("tertib") ||
    text.includes("aturan")
  ){
    knowledge += "\n\n" + tataTertib
  }

  if(
    text.includes("faq")
  ){
    knowledge += "\n\n" + faq
  }

  return knowledge

}
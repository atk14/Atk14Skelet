/*
 * Dual licensed under the MIT and GPL licenses:
 * 	http://www.opensource.org/licenses/mit-license.php
 * 	http://www.gnu.org/licenses/gpl.html
 */

/**
 * Changes obfuscated email addresses to normal email links.
 *
 * @name            Unobfuscate
 * @version         2.0
 * @author          Bohdan Ganicky
 * @param atstring  String to be changed to '@' sign. (default: '[at-sign]')
 * @param dotstring String to be changed to '.' sign. (default: '[dot-sign]')
 * @desc            Simple class for defuscating obfuscated email
 *                  No dependencies, no jQuery required.
 *                  addresses, changing them to normal 'mailto style' links.
 *                  Good to use when you don't have control over what's coming
 *                  from the server. Accepts various formats including links:
 *                  - <span>bob[at-sign]mail[dot-sign]xy</span>
 *                  - <a href="mailto:bob[at-sign]mail[dot-sign]xy">contact</a>
 *                  - <a href="mailto:bob[at-sign]mail[dot-sign]xy">bob[at-sign]m
 *                  ail[dot-sign]xy</a> etc.
 */


export default class UnobfuscateEmails {
  constructor() {}

  static #defaults = {
    atstring:  "[at-sign]",
    dotstring: "[dot-sign]",
    selector: ".atk14_no_spam"
  }

  static #gimmeMail( str, at, dot ) {
    // simply return defuscated address, k thx bai
    return str.replace( at, "@", "g" ).replace(dot, ".", "g");
  }


  static unobfuscate(options) {

    // merge default and user-defined options
    let opts = { ...this.#defaults, ...options };

    // get obfuscated mails and remove already defuscated items from the set
    let emails = document.querySelectorAll( opts.selector + ":not(.unobfuscated)" );

    if( emails.length === 0 ) {
      return;
    }

    [...emails].forEach( (emailElement) => {

      let address = this.#gimmeMail( emailElement.innerText, opts.atstring, opts.dotstring );
      let text = emailElement.dataset.text || address;
      let attrs = emailElement.dataset.attrs || {};

      // modify the text first
      emailElement.innerText = address;      

      // if emailElement is link -> modify the href
      if( emailElement.tagName.toLowerCase() === "a" ) {
        emailElement.setAttribute( "href", this.#gimmeMail( emailElement.getAttribute( "href" ), opts.atstring, opts.dotstring ) );
      } else {
        // if not -> create a link
        let link = document.createElement( "a" );
        link.setAttribute( "href", "mailto:" + address );
        link.innerText = text;
        Object.keys( attrs ).forEach( ( key ) => {
          link.setAttribute( key, attrs[ key ] );
        });
        emailElement.innerHTML = "";
        emailElement.appendChild(link);
      }
			
      // prevent future defuscation (for later calls on newly added elements)
      emailElement.classList.add("unobfuscated");
      
    } );
  }
};

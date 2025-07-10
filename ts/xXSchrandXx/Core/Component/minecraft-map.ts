/**
 * The `<minecraft-map>` element creates a minecraft-map via LeafletJS.
 *
 * @author Marcel Werk - Modified by xXSchrandXx
 * @copyright 2001-2022 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */

import * as D from "dynmap";

declare class DynMap extends D.DynMap {// modify DynMap to be exported
  constructor(options: D.Options);
}

export class MinecraftMapElement extends HTMLElement {
  #map?: any;
  #container?: HTMLElement;
  #mapLoaded: Promise<void>;
  #mapLoadedResolve?: () => void;
  #rendered = false;

  constructor() {
    super();

    if (window.ENABLE_DEBUG_MODE) {
      console.info("Creating MinecrafttMapElement...");
    }

    if (window.dynmap !== undefined) {
      throw new Error("A dynmap instance already exists. Only one minecraft-map element is allowed per page.");
    }

    this.#mapLoaded = new Promise<void>((resolve) => {
      this.#mapLoadedResolve = resolve;
    });
  }

  connectedCallback() {
    if (!this.hidden) {
      this.#render();
    }
  }

  attributeChangedCallback(name: string, oldValue: string | null, newValue: string | null): void {
    if (name === "hidden" && newValue === null) {
      this.#render();
    }
  }

  static get observedAttributes(): string[] {
    return ["hidden"];
  }

  #render(): void {
    if (this.#rendered) {
      return;
    }
    if (window.ENABLE_DEBUG_MODE) {
      console.info("Rendering MinecrafttMapElement...");
    }

    this.#validate();

    this.#rendered = true;

    this.#initMap();
  }

  #validate(): void {
    if (!this.id) {
      throw new Error("The 'id' attribute is required for the minecraft-map element.");
    }
  }

  async #initMap(): Promise<void> {
    if (window.ENABLE_DEBUG_MODE) {
      console.info("Initializing MinecrafttMapElement " + this.id + "...");
    }
    this.#container = document.createElement("div");
    this.#container.id = "mcmap" + this.id;
    this.#container.className = "dynmap";
    this.appendChild(this.#container);

    const options: D.Options = {
      container: "#" + this.#container.id,
      url: {
        configuration: `${window.WSC_RPC_API_URL}xxschrandxx/dynmap/${this.id}/configuration`,
        update: `${window.WSC_RPC_API_URL}xxschrandxx/dynmap/${this.id}/update/{world}/{timestamp}`,
        sendmessage: `${window.WSC_RPC_API_URL}xxschrandxx/dynmap/${this.id}/sendmessage`,
        login: '', // not supported
        register: '', // not supported
        tiles: `${window.WSC_RPC_API_URL}xxschrandxx/dynmap/${this.id}/tile&tile=`,
        markers: `${window.WSC_RPC_API_URL}xxschrandxx/dynmap/${this.id}/marker&marker=`
      },
      "login-enabled": false,
      loginrequired: false
    }
    if (window.ENABLE_DEBUG_MODE) {
      console.info("Using Options: " + options);
    }

    window.dynmap = new DynMap(options);

    if (window.ENABLE_DEBUG_MODE) {
      console.info("Created Dynmap: " + window.dynmap);
    }

    if (this.#mapLoadedResolve) {
      this.#mapLoadedResolve();
      this.#mapLoadedResolve = undefined;
    }
  }

  async getMap(): Promise<D.GlobalMap> {
    await this.#mapLoaded;

    return this.#map!;
  }
}

window.customElements.define("minecraft-map", MinecraftMapElement);

export default MinecraftMapElement;

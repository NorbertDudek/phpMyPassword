<?php
$requireadmin = false;
require_once('header.php');
require_once('resources/stack.php');
?>

<h2><?php echo _("Groups"); ?>:</h2>

<div class="form-group">
	<div class="col-sm-12" style="position:relative;">
		<input type="text" class="form-control" maxlength="256" id="groupSearch" autocomplete="off" onkeyup="obslugaWyszukiwaniaGrup(this.value)" onfocus="pokazPodpowiedziGrup(this.value)">
		<div id="grupySuggestionsGroups" class="list-group" style="display:none; position:absolute; z-index:1000; width:100%; max-height:240px; overflow-y:auto; box-shadow:0 2px 6px rgba(0,0,0,0.2);"></div>
	</div>
</div>

<table class="table table-hover" id="groups_table">
	<thead>
		<tr>
			<th><?php echo _("Group Name"); ?></th>
			<th><?php echo _("Notes"); ?></th>
			<th><?php echo _("Actions"); ?></th>
		</tr>
	</thead>
	<tbody>

<?php

$grupy_do_podpowiedzi = array();

function echoGroup($level, $name, $description, $gid, $chain)  {
	global $grupy_do_podpowiedzi;
	$myRights = user_rights();
	$path = get_group_path($gid);
	$grupy_do_podpowiedzi[] = $path;
?>
			<tr data-gid="<?php echo $gid; ?>" data-chain="<?php echo $chain; ?>">
				<td><?php echo str_repeat('&nbsp;', $level *4); echo $name;?></td>
				<td><?php echo $description;?></td>
				<td>
					<?php if (($myRights & accGroupEdit) != 0) { ?>
						<a href="edit_group.php?gid=<?php echo $gid; ?>" class="btn btn-xs btn-primary"><?php echo _("Edit"); ?></a>
					<?php } ?>
					<?php if (($myRights & accGroupRemove) != 0) { ?>
						<a href="delete_group.php?gid=<?php echo $gid; ?>" class="btn btn-xs btn-danger"><?php echo _("Delete"); ?></a>
					<?php } ?>
				</td>
			</tr>
<?php

}

function list_groups($parent, $level = 0, $chain = '') {
	
	global $groupstack;
	
	if ($level >= 10)
		return;
		
	$groups = get_group_list($parent);
	
	if (!is_null($groups)) 
		foreach ($groups as $group) {
			if ((check_group_permissions($group['gid'], get_my_uid())) || am_i_admin())  {
				$id = $group['gid'];
				$new_chain = ($chain === '') ? $id : $chain.','.$id;
				echoGroup($level, $group['name'], $group['description'], $id, $chain);
				list_groups($id, $level +1, $new_chain);
				}
			}
}

list_groups(0);
	?>	




	</tbody>
</table>
<div><a href="add_group.php" class="btn btn-sm btn-primary"><?php echo _("Add new group"); ?></a></div>

<script>
const dostepneGrupyGroups = <?php echo json_encode(array_values($grupy_do_podpowiedzi), JSON_UNESCAPED_UNICODE); ?>;
</script>
<script>

function obslugaWyszukiwaniaGrup(tekst) {
  filtrujWierszeGrup('groups_table', tekst);
  zapiszSzukanyTekstGrup(tekst);
  pokazPodpowiedziGrup(tekst);
}

function pokazPodpowiedziGrup(tekst) {
  const kontener = document.getElementById('grupySuggestionsGroups');
  const szukanyTekst = tekst.toLowerCase().trim();

  if (szukanyTekst === '') {
    kontener.style.display = 'none';
    kontener.innerHTML = '';
    return;
  }

  const dopasowane = dostepneGrupyGroups.filter(g => g.toLowerCase().indexOf(szukanyTekst) > -1).slice(0, 10);

  if (dopasowane.length === 0) {
    kontener.style.display = 'none';
    kontener.innerHTML = '';
    return;
  }

  kontener.innerHTML = '';
  dopasowane.forEach(function(grupa) {
    const pozycja = document.createElement('a');
    pozycja.href = '#';
    pozycja.className = 'list-group-item';
    pozycja.textContent = grupa;
    pozycja.onclick = function(e) {
      e.preventDefault();
      wybierzGrupeGrup(grupa);
    };
    kontener.appendChild(pozycja);
  });
  kontener.style.display = 'block';
}

function wybierzGrupeGrup(grupa) {
  const pole = document.getElementById('groupSearch');
  pole.value = grupa;
  filtrujWierszeGrup('groups_table', grupa);
  zapiszSzukanyTekstGrup(grupa);
  document.getElementById('grupySuggestionsGroups').style.display = 'none';
}

// Ukryj podpowiedzi po kliknięciu poza polem wyszukiwania i listą
document.addEventListener('click', function(e) {
  const pole = document.getElementById('groupSearch');
  const kontener = document.getElementById('grupySuggestionsGroups');
  if (e.target !== pole && !kontener.contains(e.target)) {
    kontener.style.display = 'none';
  }
});

function filtrujWierszeGrup(tabelaId, tekst) {
  const tabela = document.getElementById(tabelaId);
  const wiersze = Array.from(tabela.getElementsByTagName('tr'));
  const szukanyTekst = tekst.toLowerCase();

  if (szukanyTekst === '') {
    for (let i = 1; i < wiersze.length; i++) {
      wiersze[i].style.display = '';
    }
    return;
  }

  // 1. Znajdź grupy, których nazwa/opis pasują bezpośrednio do wyszukiwanego tekstu
  const dopasowaneGid = new Set();
  for (let i = 1; i < wiersze.length; i++) {
    const wiersz = wiersze[i];
    const zawartosc = wiersz.textContent.toLowerCase();
    if (zawartosc.indexOf(szukanyTekst) > -1) {
      dopasowaneGid.add(wiersz.getAttribute('data-gid'));
    }
  }

  function chainMatches(wiersz) {
    const chain = wiersz.getAttribute('data-chain') || '';
    if (chain === '') return false;
    const ids = chain.split(',');
    return ids.some(id => dopasowaneGid.has(id));
  }

  // 2. Pokaż grupy dopasowane bezpośrednio lub przez przodka w łańcuchu (grupa nadrzędna pasuje -> pokaż wszystkie podgrupy)
  for (let i = 1; i < wiersze.length; i++) {
    const wiersz = wiersze[i];
    const gid = wiersz.getAttribute('data-gid');
    if (dopasowaneGid.has(gid) || chainMatches(wiersz)) {
      wiersz.style.display = '';
    } else {
      wiersz.style.display = 'none'; // tymczasowo, może zostać pokazany w kroku 3
    }
  }

  // 3. Pokaż grupy nadrzędne, które mają choć jedną widoczną podgrupę (przetwarzanie od dołu, dla zagnieżdżeń)
  for (let i = wiersze.length - 1; i >= 1; i--) {
    const wiersz = wiersze[i];
    if (wiersz.style.display === 'none') {
      const gid = wiersz.getAttribute('data-gid');
      const maWidocznegoPotomka = wiersze.some(w => {
        if (w === wiersz) return false;
        const chain = (w.getAttribute('data-chain') || '').split(',');
        return chain.includes(gid) && w.style.display !== 'none';
      });
      if (maWidocznegoPotomka) {
        wiersz.style.display = '';
      }
    }
  }
}

function zapiszSzukanyTekstGrup(tekst) {
  try {
    if (tekst === '') {
      localStorage.removeItem('phpMyPassword_groups_search');
    } else {
      localStorage.setItem('phpMyPassword_groups_search', tekst);
    }
  } catch (e) {
    // localStorage niedostępny (np. tryb prywatny) - ignorujemy
  }
}

function wczytajSzukanyTekstGrup() {
  try {
    return localStorage.getItem('phpMyPassword_groups_search') || '';
  } catch (e) {
    return '';
  }
}

// Przywróć zapamiętany tekst wyszukiwania po odświeżeniu strony
document.addEventListener('DOMContentLoaded', function() {
  const zapamietanyTekst = wczytajSzukanyTekstGrup();
  if (zapamietanyTekst !== '') {
    const pole = document.getElementById('groupSearch');
    pole.value = zapamietanyTekst;
    filtrujWierszeGrup('groups_table', zapamietanyTekst);
  }
});
</script>

<?php
require_once('footer.php');
?>
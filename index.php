<?php
    $exportvisible = true; 
    require_once('header.php');

	$mode = "all";
	$title = "All Passwords Available To Me";
?>



					<div class="form-group">
						<div class="col-sm-12" style="position:relative;">
							<input type="text" class="form-control" maxlength="256" id="search" autocomplete="off" onkeyup="obslugaWyszukiwania(this.value)" onfocus="pokazPodpowiedzi(this.value)">
							<div id="grupySuggestions" class="list-group" style="display:none; position:absolute; z-index:1000; width:100%; max-height:240px; overflow-y:auto; box-shadow:0 2px 6px rgba(0,0,0,0.2);"></div>
						</div>
					</div>

				<table class="table table-hover" id="passwords_table">
					<thead>
						<tr>
							<th class="col-xs-2"><?php echo _("Name"); ?></th>
							<th class="col-xs-2"><?php echo _("User name"); ?></th>
							<th class="col-xs-3"><?php echo _("Notes"); ?></th>
							<th class="col-xs-3"><?php echo _("Actions"); ?></th>
						</tr>
					</thead>
					<tbody id="password">
 <?php


function show_password($gid, $level, $chain = '') {
	$results = get_my_passwords($gid);
	foreach ($results as $entry) {
		$id =  $entry['id'];
		$name = $entry['name'];
		$login = decrypt_string($entry['login']);
		$notes = $entry['note'];
		$myRights = user_rights();
		
		?>
							<tr data-chain="<?php echo $chain;?>" onclick="window.location='show.php?id=<?php echo $id;?>'">
								<td><?php echo str_repeat('&nbsp;&nbsp;', $level *4 +4).$name; ?></td>
								<td><?php echo $login; ?></td>
								<td><?php echo $notes; ?></td>
								<td>
									<a class="btn btn-primary btn-xs" href="show.php?id=<?php echo $id;?>"><?php echo _("Show"); ?></a>
									<?php if (($myRights & accPasswordEdit) != 0) { ?>
										<a class="btn btn-primary btn-xs" href="edit.php?id=<?php echo $id;?>"><?php echo _("Edit"); ?></a>
									<?php } ?>
									<?php if (($myRights & accPasswordRemove) != 0) { ?>
										<a class="btn btn-danger btn-xs" href="delete.php?id=<?php echo $id;?>"><?php echo _("Delete"); ?></a>
									<?php } ?>
								</td>
							</tr>
		<?php
	}
}

$grupy_do_podpowiedzi = array();

function show_group($gid, $level = 0, $chain = '') {
	global $grupy_do_podpowiedzi;
	$groups = get_group_list($gid);
	foreach ($groups as $group) {
		if (check_group_permissions($group['gid'], get_my_uid()))  {
			$id = $group['gid'];
			$path = get_group_path($group['gid']);
			$description = get_group_description($group['gid']);
			$new_chain = ($chain === '') ? $id : $chain.','.$id;
			
			$grupy_do_podpowiedzi[] = $path;
			
			echo "<tr data-chain=\"".$chain."\" data-gid=\"".$id."\" style=\"background-color:#d6eaf8; padding:0\"><td style=\"padding-top: 0;padding-bottom: 0\"><h5 style \" margin-top:0\"><b>".str_repeat('&nbsp;&nbsp;', $level *4).$path."</b></h5></td><td></td><td>".$description."</td><td></td>";
			
			show_password($id, $level, $new_chain);
			
			show_group($id, $level +1, $new_chain);
		}
	}
}

show_group(0);
?>
					</tbody>
				</table>
			<!-- END all passwords owned by me -->
			<?php  ?>


				<br>
<script>
const dostepneGrupy = <?php echo json_encode(array_values($grupy_do_podpowiedzi), JSON_UNESCAPED_UNICODE); ?>;
</script>
<script>

function obslugaWyszukiwania(tekst) {
  filtrujWiersze('passwords_table', tekst);
  zapiszSzukanyTekst(tekst);
  pokazPodpowiedzi(tekst);
}

function pokazPodpowiedzi(tekst) {
  const kontener = document.getElementById('grupySuggestions');
  const szukanyTekst = tekst.toLowerCase().trim();

  if (szukanyTekst === '') {
    kontener.style.display = 'none';
    kontener.innerHTML = '';
    return;
  }

  const dopasowane = dostepneGrupy.filter(g => g.toLowerCase().indexOf(szukanyTekst) > -1).slice(0, 10);

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
      wybierzGrupe(grupa);
    };
    kontener.appendChild(pozycja);
  });
  kontener.style.display = 'block';
}

function wybierzGrupe(grupa) {
  const pole = document.getElementById('search');
  pole.value = grupa;
  filtrujWiersze('passwords_table', grupa);
  zapiszSzukanyTekst(grupa);
  document.getElementById('grupySuggestions').style.display = 'none';
}

// Ukryj podpowiedzi po kliknięciu poza polem wyszukiwania i listą
document.addEventListener('click', function(e) {
  const pole = document.getElementById('search');
  const kontener = document.getElementById('grupySuggestions');
  if (e.target !== pole && !kontener.contains(e.target)) {
    kontener.style.display = 'none';
  }
});

function jestNagl(wiersz) {
  return wiersz.hasAttribute('data-gid');
}

function filtrujWiersze(tabelaId, tekst) {
  const tabela = document.getElementById(tabelaId);
  const wiersze = Array.from(tabela.getElementsByTagName('tr'));
  const szukanyTekst = tekst.toLowerCase();

  if (szukanyTekst === '') {
    for (let i = 1; i < wiersze.length; i++) {
      wiersze[i].style.display = '';
    }
    return;
  }

  // 1. Znajdź nagłówki grup, których nazwa/opis pasują do wyszukiwanego tekstu
  const dopasowaneGid = new Set();
  for (let i = 1; i < wiersze.length; i++) {
    const wiersz = wiersze[i];
    if (jestNagl(wiersz)) {
      const zawartosc = wiersz.textContent.toLowerCase();
      if (zawartosc.indexOf(szukanyTekst) > -1) {
        dopasowaneGid.add(wiersz.getAttribute('data-gid'));
      }
    }
  }

  function chainMatches(wiersz) {
    const chain = wiersz.getAttribute('data-chain') || '';
    if (chain === '') return false;
    const ids = chain.split(',');
    return ids.some(id => dopasowaneGid.has(id));
  }

  // 2. Ustal widoczność wierszy haseł oraz nagłówków dopasowanych bezpośrednio lub przez łańcuch przodków
  for (let i = 1; i < wiersze.length; i++) {
    const wiersz = wiersze[i];
    if (jestNagl(wiersz)) {
      const gid = wiersz.getAttribute('data-gid');
      if (dopasowaneGid.has(gid) || chainMatches(wiersz)) {
        wiersz.style.display = '';
      } else {
        wiersz.style.display = 'none'; // tymczasowo, może zostać pokazany w kroku 3
      }
    } else {
      const zawartosc = wiersz.textContent.toLowerCase();
      if (chainMatches(wiersz) || zawartosc.indexOf(szukanyTekst) > -1) {
        wiersz.style.display = '';
      } else {
        wiersz.style.display = 'none';
      }
    }
  }

  // 3. Pokaż nagłówki grup, które mają choć jeden widoczny wiersz potomny (przetwarzanie od dołu, dla zagnieżdżonych podgrup)
  for (let i = wiersze.length - 1; i >= 1; i--) {
    const wiersz = wiersze[i];
    if (jestNagl(wiersz) && wiersz.style.display === 'none') {
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

function zapiszSzukanyTekst(tekst) {
  try {
    if (tekst === '') {
      localStorage.removeItem('phpMyPassword_search');
    } else {
      localStorage.setItem('phpMyPassword_search', tekst);
    }
  } catch (e) {
    // localStorage niedostępny (np. tryb prywatny) - ignorujemy
  }
}

function wczytajSzukanyTekst() {
  try {
    return localStorage.getItem('phpMyPassword_search') || '';
  } catch (e) {
    return '';
  }
}

// Przywróć zapamiętany tekst wyszukiwania po odświeżeniu strony
document.addEventListener('DOMContentLoaded', function() {
  const zapamietanyTekst = wczytajSzukanyTekst();
  if (zapamietanyTekst !== '') {
    const pole = document.getElementById('search');
    pole.value = zapamietanyTekst;
    filtrujWiersze('passwords_table', zapamietanyTekst);
  }
});
</script>

<?php require_once('footer.php'); ?>